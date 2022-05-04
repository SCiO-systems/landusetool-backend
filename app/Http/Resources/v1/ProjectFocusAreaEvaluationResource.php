<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectFocusAreaEvaluationResource extends JsonResource
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
            'project_focus_area_id' => $this->project_focus_area_id,
            'lu_class' => $this->lu_class,
            'soil_value' => $this->soil_value,
            'water_value' => $this->water_value,
            'biodiversity_value' => $this->biodiversity_value,
            'climate_change_resilience_value' => $this->climate_change_resilience_value,
            'production_value' => $this->production_value,
            'economic_viability_value' => $this->economic_viability_value,
            'food_security_value' => $this->food_security_value,
            'equality_of_opportunity_value' => $this->equality_of_opportunity_value,
            'anticipated_ld_impact' => $this->anticipated_ld_impact,
        ];
    }
}
