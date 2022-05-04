<?php

namespace App\Http\Requests\Projects;

use App\Models\ProjectWocatTechnology;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListProjectTechnologiesRequest extends FormRequest
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
            'project_focus_area_id' => 'nullable|numeric',
            'lu_class' => 'nullable|string',
            'status' => [
                'nullable',
                Rule::in([ProjectWocatTechnology::STATUS_FINAL, ProjectWocatTechnology::STATUS_PROPOSAL])
            ]
        ];
    }
}
