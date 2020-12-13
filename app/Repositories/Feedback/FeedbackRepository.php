<?php
namespace App\Repositories\Feedback;

use App\Feedback;

class FeedbackRepository implements IFeedbackRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = Feedback::buildQuery($data);
        else 
            $query = Feedback::query();

        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id) {
        return Feedback::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create($data) {
        return Feedback::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Feedback $feedback) {
        $feedback->delete();
    }
}