<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ProjectInviteResource;
use App\Http\Requests\Invites\ListInvitesRequest;
use App\Http\Requests\Invites\UpdateInviteRequest;
use App\Models\Project;
use App\Models\ProjectInvite;

class InvitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListInvitesRequest $request)
    {
        $invites = $request->user()->invites()->paginate(15);

        return ProjectInviteResource::collection($invites);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInviteRequest $request, ProjectInvite $invite)
    {
        if ($invite->status !== ProjectInvite::STATUS_PENDING) {
            return response()->json([
                'errors' => [
                    'error' => 'Invite has already been accepted or rejected.',
                ]
            ], 422);
        }

        if ($request->status === ProjectInvite::STATUS_ACCEPTED) {
            $project = Project::findOrFail($invite->project_id);
            $project->addUser($request->user()->id);
        }

        $invite->update(['status' => $request->status]);

        return new ProjectInviteResource($invite);
    }
}
