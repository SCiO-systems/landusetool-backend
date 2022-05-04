<?php

namespace App\Http\Requests\ProjectFocusAreaEvaluations;

use Illuminate\Validation\Rule;
use App\Models\ProjectFocusAreaEvaluation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectFocusAreaEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->focusAreaEvaluation->user_id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'soil_value' => 'nullable|integer',
            'water_value' => 'nullable|integer',
            'biodiversity_value' => 'nullable|integer',
            'climate_change_resilience_value' => 'nullable|integer',
            'production_value' => 'nullable|integer',
            'economic_viability_value' => 'nullable|integer',
            'food_security_value' => 'nullable|integer',
            'equality_of_opportunity_value' => 'nullable|integer',
            'anticipated_ld_impact' => ['required', 'string', Rule::in([
                ProjectFocusAreaEvaluation::LD_IMPACT_IMPROVED,
                ProjectFocusAreaEvaluation::LD_IMPACT_SLIGHTLY_IMPROVED,
                ProjectFocusAreaEvaluation::LD_IMPACT_NEUTRAL,
                ProjectFocusAreaEvaluation::LD_IMPACT_SLIGHTLY_REDUCED,
                ProjectFocusAreaEvaluation::LD_IMPACT_REDUCED,
            ])],
        ];
    }
}
