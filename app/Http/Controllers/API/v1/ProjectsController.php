<?php

namespace App\Http\Controllers\API\v1;

use Http;
use App\Models\File;
use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Utilities\SCIO\TokenGenerator;
use App\Http\Resources\v1\FileResource;
use App\Utilities\SCIO\CoordsIDGenerator;
use App\Http\Resources\v1\ProjectResource;
use App\Http\Requests\Projects\ShowProjectRequest;
use App\Http\Requests\Projects\ListProjectsRequest;
use App\Http\Requests\Projects\CreateProjectRequest;
use App\Http\Requests\Projects\DeleteProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Requests\Projects\PublishProjectRequest;
use App\Http\Resources\v1\ProjectWocatTechnologyResource;
use App\Http\Requests\Projects\ChooseProjectTechnologyRequest;
use App\Http\Requests\Projects\ListProjectTechnologiesRequest;

class ProjectsController extends Controller
{
    protected $cacheTtl;
    protected $baseURI;
    protected $requestTimeout;
    protected $token;

    public function __construct()
    {
        $this->token = (new TokenGenerator())->getToken();
        $this->cacheTtl = env('CACHE_TTL_SECONDS', 3600);
        $this->baseURI = env('SCIO_SERVICES_BASE_API_URL', '');
        $this->requestTimeout = env('REQUEST_TIMEOUT_SECONDS', 10);
    }

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
        // Save the starting details.
        // Use the frontend details to generate unique identifier for the frontend.
        // Generate tif images using identifier and country iso 3.
        // Save the urls to the database.

        $project = Project::create(
            $request->only(
                'title',
                'acronym',
                'description',
            ),
        );

        $project->setOwner($request->user()->id);

        // TODO: Maybe enable this.
        // Create the land use matrix for the project if it does not exist.
        // $project->createDefaultLandUseMatrix();

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
        // Check if the project exists.
        $foundProject = $request->user()->projects()->findOrFail($project->id);

        // Check if the project is in a DRAFT state.
        if ($foundProject->status !== Project::STATUS_DRAFT) {
            return response()->json(['errors' => [
                'error' => 'The project is not in a DRAFT state and cannot be edited.'
            ]], 422);
        }

        $foundProject->update($request->only(
            'title',
            'acronym',
            'description',
            'country_iso_code_3',
            'administrative_level',
            'uses_default_lu_classification',
            'lu_classes',
            'step',
            'custom_land_degradation_map_file_id',
            'roi_file_id'
        ));

        // If the user has sent a polygon.
        if (!empty($project->polygon)) {
            $project->setPolygon($request->polygon);

            $coordinates = data_get($request->polygon, 'features.0.geometry.coordinates');
            $identifier = (new CoordsIDGenerator($coordinates))->getId();

            $body = [
                'identifier' => $identifier,
                'country_ISO' => $request->country_iso_code_3,
                'area' => $request->polygon,
            ];

            $response = Http::timeout($this->requestTimeout)
                ->withToken($this->token)
                ->acceptJson()
                ->asJson()
                ->post("$this->baseURI/tifCropperByROI", $body);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to contact remote service for generating TIF images.'
                ], $response->status());
            }

            if ($response->ok()) {
                $project->tif_images = $response->json();
                $project->save();
            }
        }

        return new ProjectResource($foundProject);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function publish(PublishProjectRequest $request, Project $project)
    {
        // Check if the project exists.
        $foundProject = $request->user()->projects()->findOrFail($project->id);

        // Check if the project is in a DRAFT state.
        if ($foundProject->status !== Project::STATUS_DRAFT) {
            return response()->json(['errors' => [
                'error' => 'The project is not in a DRAFT state and cannot be edited.'
            ]], 422);
        }

        $project->update(['status' => Project::STATUS_PUBLISHED]);

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

    /**
     * Get the IDs of the WOCAT technologies of a project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getWocatTechnologies(ListProjectTechnologiesRequest $request, Project $project)
    {
        $technologies = $project->technologies()->with('user')->get();

        return ProjectWocatTechnologyResource::collection($technologies);
    }

    /**
     * Choose the WOCAT technology of a project.
     */
    public function chooseWocatTechnology(ChooseProjectTechnologyRequest $request, Project $project)
    {
        $foundProject = $request->user()->projects()->findOrFail($project->id);

        $foundProject->technologies()->save([
            'user_id' => $request->user()->id,
            'project_id' => $project->id,
            'technology_id' => $request->technology_id,
        ]);

        $technologies = $foundProject->technologies();

        return ProjectWocatTechnologyResource::collection($technologies);
    }
}
