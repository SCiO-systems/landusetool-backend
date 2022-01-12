<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class ChooseProjectTechnologyRequest extends FormRequest
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
        ];
    }
}
