<?php

namespace App\Http\Controllers\API\v1\Event;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Event;
use App\Repositories\Event\IEventRepository;

use App\Http\Requests\Event\CreateRequest;
use App\Http\Requests\Event\UpdateRequest;

class EventController extends ApiController {

    private $eventRepository;

    public function __construct(IEventRepository $iEventRepository) {
        $this->middleware('auth:api')->except(['list']);
        $this->eventRepository = $iEventRepository;
    }

    public function list(Request $request) {
        // $this->authorize('viewAny', Event::class);
        $events = $this->eventRepository->list($request->all(), true);
        return $this->responseWithData(200, $events);
    }

    public function details(Event $event) {
        // $this->authorize('view', $event);
        return $this->responseWithData(200, $event);
    }

    public function create(CreateRequest $request) {
        // $this->authorize('create', Event::class);
        $event = $this->eventRepository->create($request->all(), $request->files->all());
        return $this->responseWithMessageAndData(201, $event, 'Event created.');
    }

    public function update(UpdateRequest $request, Event $event) {
        // $this->authorize('update', $event);
        $event = $this->eventRepository->update($event, $request->all(), $request->files->all());
        return $this->responseWithMessageAndData(200, $event, 'Event updated.');
    }

    public function delete(Event $event) {
        // $this->authorize('delete', $event);
        $this->eventRepository->delete($event);
        return $this->responseWithMessage(200, 'Event deleted.');
    }
}
