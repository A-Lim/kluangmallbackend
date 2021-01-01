<?php

namespace App\Http\Controllers\API\v1\Feedback;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Feedback;
use App\Repositories\Feedback\IFeedbackRepository;

use App\Http\Requests\Feedback\CreateRequest;
use App\Http\Requests\Feedback\UpdateRequest;

class FeedbackController extends ApiController {

    private $feedbackRepository;

    public function __construct(IFeedbackRepository $iFeedbackRepository) {
        $this->middleware('auth:api')->except(['create']);
        $this->feedbackRepository = $iFeedbackRepository;
    }

    public function list(Request $request) {
        $this->authorize('viewAny', Feedback::class);
        $feedbacks = $this->feedbackRepository->list($request->all(), true);
        return $this->responseWithData(200, $feedbacks);
    }

    public function create(CreateRequest $request) {
        $feedback = $this->feedbackRepository->create($request->all());
        return $this->responseWithMessageAndData(201, $feedback, 'Feedback created.');
    }

    public function delete(Feedback $feedback) {
        $this->authorize('delete', $feedback);
        $this->feedbackRepository->delete($feedback);
        return $this->responseWithMessage(200, 'Feedback deleted.');
    }
}
