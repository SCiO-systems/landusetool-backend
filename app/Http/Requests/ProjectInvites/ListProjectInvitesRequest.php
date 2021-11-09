<?php

namespace App\Http\Requests\ProjectInvites;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ListProjectInvitesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isProjectOwner = Auth::user()
            ->projects()
            ->where('project_id', $this->project->id)
            ->where('role', User::ROLE_OWNER)
            ->exists();

        return $isProjectOwner;
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
