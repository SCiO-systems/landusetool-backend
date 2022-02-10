<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Project;
use App\Models\ProjectScenario;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ProjectScenarioResource;
use App\Http\Requests\ProjectScenarios\DeleteAllScenariosRequest;
use App\Http\Requests\ProjectScenarios\ListProjectScenariosRequest;
use App\Http\Requests\ProjectScenarios\CreateProjectScenarioRequest;
use App\Http\Requests\ProjectScenarios\DeleteProjectScenarioRequest;
use App\Http\Requests\ProjectScenarios\UpdateProjectScenarioRequest;

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

        return ProjectScenarioResource::collection($scenarios);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProjectScenarioRequest $request, Project $project)
    {
        $data = $request->only(['from_year', 'to_year', 'name']);
        $data['content'] = json_encode($request->content);

        $scenario = $project->scenarios()->create($data);

        return new ProjectScenarioResource($scenario);
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

        return new ProjectScenarioResource($scenario);
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

        $data = $request->only(['from_year', 'to_year', 'name']);
        $data['content'] = json_encode($request->content);

        $scenario->update($data);

        return new ProjectScenarioResource($scenario);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function clearScenarios(
        DeleteAllScenariosRequest $request,
        Project $project,
        ProjectScenario $scenario
    ) {
        $project->scenarios()->delete();

        return response()->json(null, 204);
    }
}
