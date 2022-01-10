<?php

namespace App\Http\Requests\Integrations;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class GetWocatTechnologiesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'keyword' => 'string|required',
            'from' => 'numeric|required',
            'size' => 'numeric|required',
        ];
    }
}
