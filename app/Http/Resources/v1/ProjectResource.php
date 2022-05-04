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
            'custom_land_degradation_map_file_id' => $this->custom_land_degradation_map_file_id,
            'roi_file_id' => $this->roi_file_id,
            'step' => $this->step,
            'status' => $this->status,
            'land_use_map_file_id' => $this->land_use_map_file_id,
            'preprocessing_data' => json_decode($this->preprocessing_data),
            'transition_impact_matrix_data' => json_decode($this->transition_impact_matrix_data),
            'has_edited_transition_matrix_data' => $this->has_edited_transition_matrix_data,
            'land_use_suitability_method' => $this->land_use_suitability_method,
            'land_management_sustainability_method' => $this->land_management_sustainability_method
        ];
    }
}
