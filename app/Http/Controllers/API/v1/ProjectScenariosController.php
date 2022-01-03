<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectScenarios\CreateProjectScenarioRequest;
use App\Http\Requests\ProjectScenarios\DeleteProjectScenarioRequest;
use App\Http\Requests\ProjectScenarios\ListProjectScenariosRequest;
use App\Http\Requests\ProjectScenarios\UpdateProjectScenarioRequest;
use App\Models\ProjectScenario;

class ProjectScenariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListProjectScenariosRequest $request, Project $project)
    {
        $scenarios = $project->scenarios()->get();

        return response()->json($scenarios);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProjectScenarioRequest $request, Project $project)
    {
        $scenario = $project->scenarios()->create($request->all());

        return response()->json($scenario);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(
        ListProjectScenariosRequest $request,
        Project $project,
        ProjectScenario $scenario
    ) {
        $scenario = $project->scenarios()->findOrFail($scenario->id);

        return response()->json($scenario);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(
        UpdateProjectScenarioRequest $request,
        Project $project,
        ProjectScenario $scenario
    ) {
        $scenario = $project->scenarios()->findOrFail($scenario->id);
        $scenario->update($request->all());

        return response()->json($scenario);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        DeleteProjectScenarioRequest $request,
        Project $project,
        ProjectScenario $scenario
    ) {
        $scenario = $project->scenarios()->findOrFail($scenario->id);
        $scenario->delete();

        return response()->json(null, 204);
    }
}
