<?php

namespace App\Http\Requests\ProjectFiles;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class CreateProjectFileRequest extends FormRequest
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
            'file' => 'required|file|max:50000|mimes:geotiff,geotif,tiff,tif,geojson,shp'
        ];
    }
}
