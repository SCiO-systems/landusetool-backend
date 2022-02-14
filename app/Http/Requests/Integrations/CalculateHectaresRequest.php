<?php

namespace App\Http\Requests\Integrations;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CalculateHectaresRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isProjectOwner = $this->project->owner->id === $this->user()->id;
        $isProjectFile = $this->project->files()->where('id', $this->polygon_file_id)->exists();

        return $isProjectOwner && $isProjectFile;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'polygon_file_id' => 'required|exists:project_file,id',
        ];
    }
}
