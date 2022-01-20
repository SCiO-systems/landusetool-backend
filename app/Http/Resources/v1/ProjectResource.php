<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'title' => $this->title,
            'acronym' => $this->acronym,
            'description' => $this->description,
            'country_iso_code_3' => $this->country_iso_code_3,
            'administrative_level' => $this->administrative_level,
            'users' => ProjectUserResource::collection($this->whenLoaded('users')),
            'role' => $this->pivot->role ?? null,
            'uses_default_lu_classification' => $this->uses_default_lu_classification,
            'lu_classes' => json_decode($this->lu_classes),
            'tif_images' => $this->tif_images,
            'custom_land_degradation_map_file_id' => $this->custom_land_degradation_map_file_id
        ];
    }
}
