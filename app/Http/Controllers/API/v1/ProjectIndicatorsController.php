<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectIndicators\ListProjectIndicatorsRequest;
use App\Http\Requests\ProjectIndicators\UpdateProjectIndicatorsRequest;

class ProjectIndicatorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListProjectIndicatorsRequest $request, Project $project)
    {
        $indicators = $project->indicators()->get();

        return response()->json($indicators);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectIndicatorsRequest $request, Project $project)
    {
        $project->indicators()->sync($request->indicators, true);

        return response()->json([], 204);
    }
}
