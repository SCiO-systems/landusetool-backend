<?php

namespace App\Http\Requests\Invites;

use App\Models\ProjectInvite;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInviteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $inviteBelongsToUser = $this->invite->user_id == Auth::user()->id;
        $inviteIsPending = $this->invite->status == ProjectInvite::STATUS_PENDING;
        return Auth::check() && $inviteBelongsToUser && $inviteIsPending;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => ['required', 'string', Rule::in([
                ProjectInvite::STATUS_ACCEPTED,
                ProjectInvite::STATUS_REJECTED
            ])],
        ];
    }
}
