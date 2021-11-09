<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\ListUsersRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\ShowUserRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of all the resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListUsersRequest $request)
    {
        $users = User::where('firstname', 'ilike', '%' . $request->name . '%')
            ->orWhere('lastname', 'ilike', '%' . $request->name . '%')
            ->orWhere('email', 'ilike', '%' . $request->name . '%')
            ->get();

        $users = collect($users)->filter(function ($user) use ($request) {
            if ($user->id == $request->user()->id) {
                return false;
            }
            return true;
        });

        return UserResource::collection($users);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ShowUserRequest $request, User $user)
    {
        return new UserResource($request->user());
    }

    /**
     * Create a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $data = $request->only('firstname', 'lastname', 'email', 'password');

        // Hash the password.
        $data['password'] = bcrypt($request->password);

        // Set the default identity provider.
        $data['identity_provider'] = User::IDENTITY_PROVIDER_LOCAL;

        // Create the user.
        $user = User::create($data);

        if (!$user) {
            return response()->json(['errors' => [
                'error' => 'Failed to create the user. Please try again!'
            ]], 409);
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Filter null and falsy values.
        // TODO: Check for SQLi.
        $data = collect($request->except(['avatar_url']))->filter()->all();

        // Update the user details with the new ones.
        $request->user()->update($data);

        return new UserResource($request->user());
    }
}
