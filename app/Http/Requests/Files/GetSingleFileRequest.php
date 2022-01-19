<?php

namespace App\Http\Requests\Files;

use Illuminate\Foundation\Http\FormRequest;

class GetSingleFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isFileOwner = $this->user()->id === $this->file->user_id;

        return $isFileOwner;
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
