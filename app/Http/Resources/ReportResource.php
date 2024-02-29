<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        // Determine the type of resource based on reportable_type
//        $reportableResource = $this->reportable_type === 'user' ?
//            UserResource::make($this->user) :
//            PostResource::make($this->post);
//
//        $key = $this->reportable_type === 'user' ? 'user_info' : 'post_info';

        return [
            'report_id' => $this->id,
            'report_type' => $this->reportable_type,
            'status' => $this->status,
            'reportable_info' => $this->when($this->reportable_type === 'App\Models\User', function () {
                return UserResource::make($this->reportable);
            }, $this->when($this->reportable_type === 'App\Models\Post', function () {
                return PostResource::make($this->reportable);
            })),
        ];
    }
}
