<?php

namespace App\Http\Requests\Projects;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'acronym' => 'required|string|max:50',
            'description' => 'nullable|string',
            'country_iso_code_3' => 'string|required',
            'administrative_level' => 'numeric|required',
            'polygon' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'uses_default_lu_classification' => 'required|boolean',
            'lu_classes' => 'nullable|json',
        ];
    }
}
