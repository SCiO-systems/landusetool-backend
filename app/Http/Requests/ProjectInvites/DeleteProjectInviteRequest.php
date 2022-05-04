<?php

namespace App\Http\Requests\ProjectInvites;

use Illuminate\Foundation\Http\FormRequest;

class DeleteProjectInviteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isProjectOwner = $this->project->owner->id === $this->user()->id;

        $inviteBelongsToProject = $this->project->invites()
            ->where('id', $this->invite->id)
            ->exists();

        return $isProjectOwner && $inviteBelongsToProject;
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
