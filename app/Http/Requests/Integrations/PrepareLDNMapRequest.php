<?php

namespace App\Http\Requests\Integrations;

use Illuminate\Foundation\Http\FormRequest;

class PrepareLDNMapRequest extends FormRequest
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
            'polygons_list' => 'required|array',
            'polygons_list.*' => 'required|array:value,file_id',
        ];
    }
}
