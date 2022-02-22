<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Project;
use App\Models\ProjectFocusArea;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ProjectFocusAreaResource;
use App\Http\Requests\ProjectFocusAreas\ShowProjectFocusAreaRequest;
use App\Http\Requests\ProjectFocusAreas\ListProjectFocusAreasRequest;
use App\Http\Requests\ProjectFocusAreas\CreateProjectFocusAreaRequest;
use App\Http\Requests\ProjectFocusAreas\DeleteProjectFocusAreaRequest;

class ProjectFocusAreasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListProjectFocusAreasRequest $request, Project $project)
    {
        $focusAreas = $project->focusAreas()->get();

        return ProjectFocusAreaResource::collection($focusAreas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProjectFocusAreaRequest $request, Project $project)
    {
        $data = $request->only('name', 'file_id');
        $data['user_id'] = $request->user()->id;

        $focusArea = $project->focusAreas()->create($data);

        return new ProjectFocusAreaResource($focusArea);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(
        ShowProjectFocusAreaRequest $request,
        Project $project,
        ProjectFocusArea $focusArea
    ) {
        return new ProjectFocusAreaResource($focusArea);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        DeleteProjectFocusAreaRequest $request,
        Project $project,
        ProjectFocusArea $focusArea
    ) {
        if ($focusArea->delete()) {
            return response()->json(null, 204);
        }

        return response()->json(null, 500);
    }
}
