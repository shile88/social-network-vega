<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'post_id' => $this->id,
            'created_by' => UserResource::make($this->user),
            'content' => $this->content,
            'like' => LikeResource::collection($this->whenLoaded('likes')),
            'comment' => CommentResource::collection($this->whenLoaded('comments')),
            'image_name' => $this->image_name,
            'image_path' => $this->image_path
        ];
    }
}
