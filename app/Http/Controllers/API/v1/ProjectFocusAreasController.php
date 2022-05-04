<?php

namespace App\Http\Controllers\API\v1;

use Log;
use App\Models\Project;
use App\Models\ProjectFocusArea;
use App\Models\ProjectFile;
use App\Http\Controllers\Controller;
use App\Utilities\SCIO\LandCoverClassExtractor;
use Illuminate\Support\Facades\Storage;
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

        try {
            $file = Storage::get(ProjectFile::find($data['file_id'])->path);
            $data['extracted_classes'] = (new LandCoverClassExtractor($project, $file))->extractClasses();
            if (count($data['extracted_classes']) === 0) {
                Storage::delete(ProjectFile::find($data['file_id'])->path);
                return response()->json([
                    'errors' => [
                        'error' => 'Could not find any land use types in the polygon provided.',
                    ]
                ], 422);
            }
            $data['extracted_classes'] = json_encode($data['extracted_classes']);
        } catch (\Exception $ex) {
            Log::error('During creation of focus area with file_id: ' . $data['file_id'] . ' an error
                occured while trying to extract land cover classses: ' . $ex->getMessage());
            return response()->json(null, 500);
        }

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
        $isFileDeleted = Storage::delete($focusArea->file->path);
        if ($isFileDeleted && $focusArea->file()->delete() && $focusArea->delete()) {
            return response()->json(null, 204);
        }

        return response()->json(null, 500);
    }
}
