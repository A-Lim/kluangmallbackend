<?php

namespace App\Http\Controllers\API\v1\Notification;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Notification;
use App\Repositories\Notification\INotificationRepository;

class NotificationController extends ApiController {

    private $notificationRepository;

    public function __construct(INotificationRepository $iNotificationRepository) {
        $this->middleware('auth:api');
        $this->notificationRepository = $iNotificationRepository;
    }

    public function list(Request $request) {
        $user = auth()->user();
        $notifications = $this->notificationRepository->list($user, $request->all, true);
        return $this->responseWithData(200, $notifications);
    }

    public function read(Notification $notification) {
        $user = auth()->user();
        if ($notification->user_id != $user->id)
            return $this->responseWithMessage(400, 'Unable to mark a notification that does not belong to you as read.');

        $this->notificationRepository->read($notification);
        return $this->responseWithMessage(200, 'Notification successfully mark as read.');
    }

    public function readAll() {
        $user = auth()->user();
        $this->notificationRepository->markAllAsRead($user);
        return $this->responseWithMessage(200, 'All notifications successfully mark as read.');
    }
}
