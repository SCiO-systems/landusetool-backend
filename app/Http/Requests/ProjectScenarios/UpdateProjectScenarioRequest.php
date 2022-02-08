<?php

namespace App\Http\Requests\ProjectScenarios;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectScenarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isProjectOwner = $this->project->owner->id === $this->user()->id;

        return $isProjectOwner;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string',
            'from_year' => 'nullable|numeric',
            'to_year' => 'nullable|numeric',
            'content' => 'nullable|string'
        ];
    }
}
