<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ProjectResource;
use App\Http\Requests\Projects\ShowProjectRequest;
use App\Http\Requests\Projects\ListProjectsRequest;
use App\Http\Requests\Projects\CreateProjectRequest;
use App\Http\Requests\Projects\DeleteProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListProjectsRequest $request)
    {
        $projects = $request->user()->projects()->paginate(15);

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProjectRequest $request)
    {
        $project = Project::create($request->only('title', 'acronym', 'description'));
        $project->setOwner($request->user()->id);

        return new ProjectResource($project);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowProjectRequest $request, Project $project)
    {
        $project = $request->user()->projects()->with('users')->findOrFail($project->id);

        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $foundProject = $request->user()->projects()->findOrFail($project->id);
        $foundProject->update($request->only('title', 'acronym', 'description'));

        return new ProjectResource($foundProject);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteProjectRequest $request, Project $project)
    {
        $foundProject = $request->user()->projects()->findOrFail($project->id);

        if ($foundProject->delete()) {
            return response()->json(null, 204);
        }

        return response()->json(null, 500);
    }
}
