<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\User\UserResource;

/**
 * @mixin \App\Models\User\Admin\Report
 */
class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sender' => new UserResource($this->sender),
            'report_reason' => [
                'id' => $this->report_reason->id,
                'reason' => $this->report_reason->reason
            ],
            'note' => $this->report_note,
            'type' => $this->type,
            'reportable' => [
                'id' => $this->reportable_id,
                'author' => $this->reportable->model_author,
                'url' => $this->reportable->model_url,
                'content' => $this->reportable->reportable_content,
                'image' => $this->reportable->reportable_image
            ]
        ];
    }
}
