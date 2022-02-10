<?php

namespace App\Http\Requests\ProjectScenarios;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectScenarioRequest extends FormRequest
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
            'name' => 'required|string',
            'from_year' => 'required|numeric',
            'to_year' => 'required|numeric',
            'content' => 'nullable'
        ];
    }
}
