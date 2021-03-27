<?php

namespace App\Http\Resources\Announcement;

use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'publish_at' => $this->publish_at,
            'has_content' => $this->has_content,
            'content' => $this->content,
            'image' => $this->image,
            'status' => $this->remark,
            'created_at' => $this->created_at,
        ];
    }
}
