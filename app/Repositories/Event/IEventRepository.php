<?php
namespace App\Repositories\Event;

use App\Event;

interface IEventRepository {
    /**
     * List all events
     * @param array $data
     * @param bool $paginate = false
     * @return array [event]
     */
    public function list($data, $paginate = false);

    /**
     * Create Event
     * @param array $data
     * @param array $files
     * @return Event
     */
    public function create($data, $files);

    /**
     * Update Event
     * @param Event $event
     * @param array $data
     * @param array $files
     * @return Event
     */
    public function update(Event $event, $data, $files);


    /**
     * Delete Event
     * @return null
     */
    public function delete(Event $event);
}