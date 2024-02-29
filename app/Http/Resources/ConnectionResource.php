<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConnectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $checkUserId = $this->pivot ? $this->pivot->user_id : $this->friend_id;
        $checkStatus = $this->pivot ? $this->pivot->status : $this->status;
        $checkConnId = $this->pivot ? $this->pivot->id : $this->id;


        return [
            'id' => $checkConnId,
            'user' => UserResource::make(User::find($checkUserId)),
            'status' => $checkStatus ?: $this->status,
        ];
    }
}
