<?php

namespace App\Http\Requests\LandCover;

use Illuminate\Foundation\Http\FormRequest;

class GetLandCoverPercentagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isProjectOwner = $this->project->owner->id === $this->user()->id;
        $isProjectUser = $this->project->users()->where('user_id', $this->user()->id)->exists();

        return $isProjectOwner || $isProjectUser;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'most_recent_year' => 'boolean',
        ];
    }
}
