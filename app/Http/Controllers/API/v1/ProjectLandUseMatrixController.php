<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectLandUseMatrix\ListProjectLandUseMatrixRequest;
use App\Http\Requests\ProjectLandUseMatrix\UpdateProjectLandUseMatrixRequest;

class ProjectLandUseMatrixController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListProjectLandUseMatrixRequest $request, Project $project)
    {
        $projectLandUseMatrix = $project->landUseMatrix()->firstOrFail();

        return response()->json($projectLandUseMatrix);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectLandUseMatrixRequest $request, Project $project)
    {
        return response()->json(null, 501);
    }
}
