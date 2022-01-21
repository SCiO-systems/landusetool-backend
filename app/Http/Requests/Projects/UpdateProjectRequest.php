<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'title' => 'string',
            'acronym' => 'string|max:50',
            'description' => 'nullable|string',
            'country_iso_code_3' => 'nullable|string',
            'administrative_level' => 'nullable|numeric',
            'polygon' => 'nullable',
            'uses_default_lu_classification' => 'boolean',
            'lu_classes' => 'nullable',
            'step' => 'nullable|string',
            'custom_land_degradation_map_file_id' => 'nullable|exists:files,id',
            'roi_file_id' => 'nullable|exists:files,id',
        ];
    }
}
