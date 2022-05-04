<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class ProposeProjectTechnologyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isProjectUser = $this->project->users()->where('user_id', $this->user()->id)->exists();

        return $isProjectUser;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'technology_id' => 'string|required',
            'project_focus_area_id' => 'integer|required',
            'lu_class' => 'string|required',
            'soil_value' => 'required|integer',
            'water_value' => 'required|integer',
            'biodiversity_value' => 'required|integer',
            'climate_change_resilience_value' => 'required|integer',
            'production_value' => 'required|integer',
            'economic_viability_value' => 'required|integer',
            'food_security_value' => 'required|integer',
            'equality_of_opportunity_value' => 'required|integer',
        ];
    }
}
