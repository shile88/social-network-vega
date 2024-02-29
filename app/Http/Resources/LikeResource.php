<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'like_id' => $this->id,
            'created_by' => UserResource::make($this->user),
            'liked_post' => PostResource::make($this->post),
        ];
    }
}
