<?php

namespace App\Http\Controllers\API\v1;

use Storage;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\UserAvatarResource;
use App\Http\Requests\UserAvatar\DeleteUserAvatarRequest;
use App\Http\Requests\UserAvatar\UpdateUserAvatarRequest;

class UserAvatarController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserAvatarRequest $request, User $user)
    {
        $user->avatar_url = $request->file('avatar')->store('avatars', 'public');
        $user->save();
        return new UserAvatarResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteUserAvatarRequest $request, User $user)
    {
        Storage::disk('public')->delete($user->avatar_url);
        $user->avatar_url = null;
        $user->save();

        return new UserAvatarResource($user);
    }
}
