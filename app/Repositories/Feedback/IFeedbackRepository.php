<?php
namespace App\Repositories\Feedback;

use App\Feedback;

interface IFeedbackRepository {
    /**
     * List all feedbacks
     * @param array $data
     * @param bool $paginate = false
     * @return array [Feedback]
     */
    public function list($data, $paginate = false);

    /**
     * Create feedback
     * @param array $data
     * @return Feedback
     */
    public function create($data);


    /**
     * Delete Event
     * @return null
     */
    public function delete(Feedback $feedback);
}