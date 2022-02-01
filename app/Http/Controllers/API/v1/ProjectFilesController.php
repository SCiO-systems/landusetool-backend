<?php

namespace App\Http\Controllers\API\v1;

use Str;
use Storage;
use App\Models\File;
use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FileResource;
use App\Http\Requests\Files\DeleteFileRequest;
use App\Http\Requests\Files\UploadFileRequest;
use App\Http\Requests\Files\GetSingleFileRequest;
use App\Http\Requests\ProjectFiles\CreateProjectFileRequest;
use App\Http\Requests\ProjectFiles\DeleteProjectFileRequest;
use App\Http\Requests\ProjectFiles\ListProjectFilesRequest;
use App\Http\Requests\ProjectFiles\ShowProjectFileRequest;
use App\Http\Resources\v1\ProjectFileResource;
use App\Models\ProjectFile;

class ProjectFilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListProjectFilesRequest $request, Project $project)
    {
        $files = $project->files()->get();

        return ProjectFileResource::collection($files);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProjectFileRequest $request, Project $project)
    {
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $validExtensions = ['geotiff', 'geotif', 'tiff', 'tif', 'geojson', 'shp'];

        $name = $file->hashName($project->id);

        if (!in_array($extension, $validExtensions)) {
            return response()->json([
                'errors' => [
                    'error' => 'Invalid file extension. Τhe file must be a file of type: geotiff, geotif, tiff, tif, geojson, shp.',
                ],
            ], 422);
        }

        $created = null;
        if (Storage::put($name, file_get_contents($file))) {
            $created = ProjectFile::create([
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
                'path' => $name,
                'filename' => $filename,
            ]);
        }

        return new ProjectFileResource($created);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowProjectFileRequest $request, Project $project, ProjectFile $file)
    {
        $file = $project->files()->find($file->id);

        return new ProjectFileResource($file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteProjectFileRequest $request, Project $project, ProjectFile $file)
    {
        $isFileDeleted = Storage::delete($file->path);

        if ($isFileDeleted) {
            $isDbEntryDeleted = $file->delete();
            if ($isDbEntryDeleted) {
                return response()->json([], 204);
            }
        }

        return response()->json(['errors' => [
            'error' => 'Something went wrong'
        ]], 400);
    }
}
