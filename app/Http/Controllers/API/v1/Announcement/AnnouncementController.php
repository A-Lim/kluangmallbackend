<?php

namespace App\Http\Controllers\API\v1\Announcement;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

use App\Announcement;
use App\Repositories\UserGroup\IUserGroupRepository;
use App\Repositories\Announcement\IAnnouncementRepository;

use App\Http\Requests\Announcement\CreateRequest;
use App\Http\Requests\Announcement\UpdateRequest;
use App\Http\Requests\Announcement\RejectRequest;

use App\Notifications\Announcement\AnnouncementPublished;
use App\Notifications\Announcement\AnnouncementActioned;

class AnnouncementController extends ApiController {

    private $announcementRepository;
    private $userGroupRepository;

    public function __construct(IAnnouncementRepository $iAnnouncementRepository,
        IUserGroupRepository $iUserGroupRepository) {
        $this->middleware('auth:api')->except(['details']);
        $this->announcementRepository = $iAnnouncementRepository;
        $this->userGroupRepository = $iUserGroupRepository;
    }

    public function list(Request $request) {
        $user = auth()->user();
        $data = $request->all();

        if (!$user->isAdmin()) {
            if ($user->merchant != null)
                $data['merchant_id'] = $user->merchant->id;
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
        return $this->responseWithData(200, $announcement);
    }

    public function create(CreateRequest $request) {
        $user = auth()->user();
        $data = $request->all();

        // is user cannot take action to announcement
        // unset audience and status and will be defaulted to audience users and status pending
        if ($user->cannot('action', Announcement::class)) {
            unset($data['audience']);
            unset($data['status']);
        }

        // $this->authorize('create', Announcement::class);
        $merchant = $user->merchant;

        $announcement = $this->announcementRepository->create($data, $merchant, $request->files->all());

        if ($announcement->status == Announcement::STATUS_PUBLISHED)
            Notification::send(null, new AnnouncementPublished($announcement));

        return $this->responseWithMessageAndData(201, $announcement, 'Announcement created.');
    }

    public function update(UpdateRequest $request, Announcement $announcement) {
        // $this->authorize('update', $announcement);

        if ($announcement->status == Announcement::STATUS_PUBLISHED)
            return $this->responseWithMessage(400, 'Unable to update announcement that has already been published.');

        $announcement = $this->announcementRepository->update($announcement, $request->all(), $request->files->all());

        if ($announcement->status == Announcement::STATUS_PUBLISHED)
            Notification::send(null, new AnnouncementPublished($announcement));

        return $this->responseWithMessageAndData(200, $announcement, 'Announcement updated.');
    }

    public function approve(Request $request, Announcement $announcement) {
        // $this->authorize('action', $announcement);
        if ($announcement->status == Announcement::STATUS_PUBLISHED)
            return $this->responseWithMessage(400, 'Unable to approve a published announcement.');
        
        $announcement = $this->announcementRepository->approve($announcement, $request->all());
        $merchant = $announcement->merchant;

        // notify merchant
        if ($merchant != null)
            $merchant->notify(new AnnouncementActioned($announcement));

        // push notification to audience
        Notification::send(null, new AnnouncementPublished($announcement));
        return $this->responseWithMessageAndData(200, $announcement, 'Announcement approved.');
    }

    public function reject(RejectRequest $request, Announcement $announcement) {
        // $this->authorize('action', $announcement);

        if ($announcement->status == Announcement::STATUS_PUBLISHED)
            return $this->responseWithMessage(400, 'Unable to reject a published announcement.');

        $announcement = $this->announcementRepository->reject($announcement, $request->all());
        $merchant = $announcement->merchant;

        // notify merchant
        if ($merchant != null)
            $merchant->notify(new AnnouncementActioned($announcement));
        
        return $this->responseWithMessageAndData(200, $announcement, 'Announcement rejected.');
    }
}
