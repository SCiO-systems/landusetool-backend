<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectWocatTechnologyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'technology_id' => $this->technology_id,
            'lu_class' => $this->lu_class,
            'status' => $this->status,
            'user' => new ProjectUserResource($this->whenLoaded('user')),
            'focus_area' => new ProjectFocusAreaResource($this->whenLoaded('focusArea')),
            'votes' => ProjectWocatTechnologyVoteResource::collection($this->whenLoaded('votes')),
        ];
    }
}
