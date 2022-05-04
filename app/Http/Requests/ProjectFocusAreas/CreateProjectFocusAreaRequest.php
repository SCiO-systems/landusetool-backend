<?php

namespace App\Http\Requests\ProjectFocusAreas;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectFocusAreaRequest extends FormRequest
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
            'name' => 'string|required',
            'file_id' => 'required|exists:project_file,id'
        ];
    }
}
