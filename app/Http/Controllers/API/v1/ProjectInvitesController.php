<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Project;
use App\Models\ProjectInvite;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ProjectInviteResource;
use App\Http\Requests\ProjectInvites\ListProjectInvitesRequest;
use App\Http\Requests\ProjectInvites\CreateProjectInviteRequest;
use App\Http\Requests\ProjectInvites\DeleteProjectInviteRequest;
use App\Http\Resources\v1\UserResource;

class ProjectInvitesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListProjectInvitesRequest $request, Project $project)
    {
        $invites = ProjectInvite::where('project_id', $project->id)->paginate();

        return ProjectInviteResource::collection($invites);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProjectInviteRequest $request, Project $project)
    {
        $users = collect($request->user_ids)->unique();
        $existingUsers = $project->users()->whereIn('user_id', $users)->get();
        $project->inviteUsers($users);

        return response()->json([
            'existing_users' => UserResource::collection($existingUsers)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        DeleteProjectInviteRequest $request,
        Project $project,
        ProjectInvite $projectInvite
    ) {
        if ($projectInvite->delete()) {
            return response()->json(null, 204);
        }

        return response()->json(null, 500);
    }
}
