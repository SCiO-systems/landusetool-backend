<?php

namespace App\Http\Requests\Integrations;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class ListLDNTargetsRequest extends FormRequest
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
            'country_iso_code_3' => 'string|required'
        ];
    }
}
