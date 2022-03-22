<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Project;
use App\Models\ProjectFocusAreaEvaluation;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ProjectFocusAreaEvaluationResource;
use App\Http\Requests\ProjectFocusAreaEvaluations\CreateProjectFocusAreaEvaluationRequest;
use App\Http\Requests\ProjectFocusAreaEvaluations\ListProjectFocusAreaEvaluationsRequest;
use App\Http\Requests\ProjectFocusAreaEvaluations\ShowProjectFocusAreaEvaluationRequest;
use App\Http\Requests\ProjectFocusAreaEvaluations\UpdateProjectFocusAreaEvaluationRequest;

class ProjectFocusAreaEvaluationsController extends Controller
{

    /**
     * Display all the resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(
        ListProjectFocusAreaEvaluationsRequest $request,
        Project $project,
    ) {
        $focusAreaIds = $project->focusAreas()->pluck('id')->toArray();
        $evaluations = ProjectFocusAreaEvaluation::whereIn('project_focus_area_id', $focusAreaIds)
            ->where('user_id', $request->user()->id)
            ->get();
        return ProjectFocusAreaEvaluationResource::collection($evaluations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProjectFocusAreaEvaluationRequest $request, Project $project)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $focusAreaEvaluation = ProjectFocusAreaEvaluation::create($data);

        return new ProjectFocusAreaEvaluationResource($focusAreaEvaluation);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(
        ShowProjectFocusAreaEvaluationRequest $request,
        Project $project,
        ProjectFocusAreaEvaluation $focusAreaEvaluation
    ) {
        return new ProjectFocusAreaEvaluationResource($focusAreaEvaluation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(
        UpdateProjectFocusAreaEvaluationRequest $request,
        Project $project,
        ProjectFocusAreaEvaluation $focusAreaEvaluation
    ) {
        $focusAreaEvaluation->update($request->validated());
        return new ProjectFocusAreaEvaluationResource($focusAreaEvaluation);
    }
}
