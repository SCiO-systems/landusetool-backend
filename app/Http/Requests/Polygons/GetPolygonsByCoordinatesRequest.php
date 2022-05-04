<?php

namespace App\Http\Requests\Polygons;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class GetPolygonsByCoordinatesRequest extends FormRequest
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
            'administrative_level' => 'required|numeric',
            'point' => 'required'
        ];
    }
}
