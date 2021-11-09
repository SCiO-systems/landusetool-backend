<?php

namespace App\Http\Requests\Projects;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class ShowProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isProjectUser = Auth::user()
            ->projects()
            ->where('project_id', $this->project->id)
            ->exists();
        return $isProjectUser;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
