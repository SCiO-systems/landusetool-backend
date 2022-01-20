<?php

namespace App\Http\Controllers\API\v1;

use Str;
use Storage;
use App\Models\File;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FileResource;
use App\Http\Requests\Files\ListFilesRequest;
use App\Http\Requests\Files\DeleteFileRequest;
use App\Http\Requests\Files\UploadFileRequest;
use App\Http\Requests\Files\GetSingleFileRequest;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListFilesRequest $request)
    {
        $files = $request->user()->files()->get();

        return FileResource::collection($files);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UploadFileRequest $request)
    {
        $file = $request->file('file');
        $name = Str::uuid();

        $created = null;
        if (Storage::put($name, $file)) {
            $created = File::create([
                'user_id' => $request->user()->id,
                'path' => $name,
                'filename' => $file->getClientOriginalName(),
            ]);
        }

        return new FileResource($created);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(GetSingleFileRequest $request, File $file)
    {
        return new FileResource($file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteFileRequest $request, File $file)
    {
        $isFileDeleted = Storage::delete($file->path);

        if ($isFileDeleted) {
            $dbEntryDeleted = $file->delete();
            if ($dbEntryDeleted) {
                return response()->json([], 204);
            }
        }

        return response()->json(['errors' => [
            'error' => 'Something went wrong'
        ]], 400);
    }
}
