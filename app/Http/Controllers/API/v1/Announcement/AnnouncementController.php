<?php

namespace App\Http\Controllers\API\v1\Announcement;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

use App\Announcement;
use App\Repositories\UserGroup\IUserGroupRepository;
use App\Repositories\Announcement\IAnnouncementRepository;
use App\Repositories\SystemSetting\ISystemSettingRepository;
use App\Repositories\Merchant\IMerchantAccountRepository;

use App\Http\Requests\Announcement\CreateRequest;
use App\Http\Requests\Announcement\UpdateRequest;
use App\Http\Requests\Announcement\RejectRequest;

use App\Http\Resources\Announcement\AnnouncementResource;
use App\Notifications\Announcement\AnnouncementPublished;
use App\Notifications\Announcement\AnnouncementActioned;

class AnnouncementController extends ApiController {

    private $announcementRepository;
    private $userGroupRepository;
    private $systemRepository;
    private $merchantAccountRepository;

    public function __construct(IAnnouncementRepository $iAnnouncementRepository,
        IUserGroupRepository $iUserGroupRepository,
        ISystemSettingRepository $iSystemSettingRepository,
        IMerchantAccountRepository $iMerchantAccountRepository) {
        $this->middleware('auth:api');
        $this->announcementRepository = $iAnnouncementRepository;
        $this->userGroupRepository = $iUserGroupRepository;
        $this->systemRepository = $iSystemSettingRepository;
        $this->merchantAccountRepository = $iMerchantAccountRepository;
    }

    public function list(Request $request) {
        // $this->authorize('viewAny', Announcement::class);
        $user = auth()->user();
        $data = $request->all();

        // if not admin
        // only return merchant's announcement
        if (!$user->isAdmin()) {
            if ($user->merchant != null)
                $data['merchant_id'] = 'equals:'.$user->merchant->id;
            else 
                return $this->responseWithMessage(400, 'This is not a merchant account.');
        }

        $announcements = $this->announcementRepository->list($data, true);
        return $this->responseWithData(200, $announcements);
    }

    public function pendingCount(Request $request) {
        $count = $this->announcementRepository->pendingCount();
        return $this->responseWithData(200, $count);
    }

    public function details(Announcement $announcement) {
        // $this->authorize('viewAny', $announcement);
        return $this->responseWithData(200, $announcement);
    }

    public function create(CreateRequest $request) {
        // $this->authorize('create', Announcement::class);
        $user = auth()->user();
        $data = $request->all();

        // is user cannot take action to announcement
        // unset audience and status and will be defaulted to audience users and status pending
        if ($user->cannot('action', Announcement::class)) {
            unset($data['audience']);
            unset($data['status']);
            unset($data['publish_now']);
        }

        // amount of credit required to create an announcement
        $announcementCreditSetting = $this->systemRepository->findByCode('credit_announcement');
        if ($announcementCreditSetting == null)
            return $this->responseWithMessage(400, 'Announcement Credit has not been set up.');

        $announcement_credit_price = (int) $announcementCreditSetting->value;
        
        // if current user is merchant
        $merchant = $user->merchant;
        if ($merchant) {
            // check if enough credit
            if ($merchant->account->credit < $announcement_credit_price)
                return $this->responseWithMessage(400, 'Insufficient credit to create announcement.');

            // deduct credit
            $transaction = $this->merchantAccountRepository->deduct($merchant, [
                'title' => 'Create Announcement',
                'credit' => $announcement_credit_price,
            ]);
        }

        $announcement = $this->announcementRepository->create($data, $announcement_credit_price, $merchant, $request->files->all());

        // if user is admin
        // and publish now
        if ($user->isAdmin() && $request->filled('publish_now') && (bool)$request->publish_now == true)
            Notification::send(null, new AnnouncementPublished($announcement));

        return $this->responseWithMessageAndData(201, $announcement, 'Announcement created.');
    }

    public function update(UpdateRequest $request, Announcement $announcement) {
        $this->authorize('update', $announcement);
        $user = auth()->user();
        $data = $request->all();

        // is user cannot take action to announcement
        // unset audience and status and will be defaulted to audience users and status pending
        if ($user->cannot('action', Announcement::class)) {
            unset($data['audience']);
            unset($data['status']);
            unset($data['publish_now']);
        }

        if ($announcement->status == Announcement::STATUS_PUBLISHED)
            return $this->responseWithMessage(400, 'Unable to update announcement that has already been published.');

        $announcement = $this->announcementRepository->update($announcement, $data, $request->files->all());
        $merchant = $announcement->merchant;

        // notify merchant
        if ($merchant != null && $announcement->status != Announcement::STATUS_PENDING)
            $merchant->notify(new AnnouncementActioned($announcement));

        // if user is admin
        // and publish now
        if ($user->isAdmin() && $request->filled('publish_now') && (bool)$request->publish_now == true)
            Notification::send(null, new AnnouncementPublished($announcement));

        return $this->responseWithMessageAndData(200, $announcement, 'Announcement updated.');
    }

    public function approve(Request $request, Announcement $announcement) {
        $this->authorize('action', $announcement);
        if ($announcement->status == Announcement::STATUS_PUBLISHED)
            return $this->responseWithMessage(400, 'Unable to approve a published announcement.');
        
        $announcement = $this->announcementRepository->approve($announcement, $request->all());
        $merchant = $announcement->merchant;

        // notify merchant
        if ($merchant != null)
            $merchant->notify(new AnnouncementActioned($announcement));

        return $this->responseWithMessageAndData(200, $announcement, 'Announcement approved.');
    }

    public function reject(RejectRequest $request, Announcement $announcement) {
        $this->authorize('action', $announcement);

        if ($announcement->status == Announcement::STATUS_PUBLISHED)
            return $this->responseWithMessage(400, 'Unable to reject a published announcement.');

        $announcement = $this->announcementRepository->reject($announcement, $request->all());
        $merchant = $announcement->merchant;
        
        if ($merchant != null) {
            // notify merchant
            $merchant->notify(new AnnouncementActioned($announcement));
            // return back credit
            $transaction = $this->merchantAccountRepository->reCredit($merchant, [
                'title' => 'Create Announcement Refund',
                'credit' => $announcement->credit_paid
            ]);
        }
    
        return $this->responseWithMessageAndData(200, $announcement, 'Announcement rejected.');
    }
}
