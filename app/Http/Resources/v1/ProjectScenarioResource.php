<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectScenarioResource extends JsonResource
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
            'project' => new ProjectResource($this->project),
            'name' => $this->name,
            'from_year' => $this->from_year,
            'to_year' => $this->to_year,
            'content' => json_decode($this->content)
        ];
    }
}
