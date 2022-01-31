<?php

namespace App\Jobs;

use Http;
use Log;
use Exception;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use App\Utilities\SCIO\AWSTokenGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PreprocessProjectData implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The projects to preprocess.
     */
    protected $projects;

    /**
     * The token to use for the request.
     */
    protected $token;

    /**
     * The maximum time in seconds to allow before the request times out.
     */
    protected $requestTimeout;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->token = (new AWSTokenGenerator())->getToken();
        $this->requestTimeout = 60; // in seconds.

        // Get the oldest projects first.
        $this->projects = Project::where('status', Project::STATUS_PREPROCESSING)
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (empty($this->projects)) {
            Log::info('No projects to preprocess.');
            return;
        }

        $defaultDatasetUrl  = env('SCIO_DEFAULT_DATASET_PREPROCESSING_URL');
        $customDatasetUrl   = env('SCIO_CUSTOM_DATASET_PREPROCESSING_URL');

        foreach ($this->projects as $project) {

            Log::info('Starting preprocessing for project.', ['project' => $project->id]);

            $data = [
                'project_id' => (string) $project->id,
                'ROI' => json_decode($project->polygon, true)
            ];

            // The project uses custom LU classification.
            if (!$project->uses_default_lu_classification) {
                Log::info('Project ' . $project->id . ' uses custom classification.');

                $ldmUrl = Storage::url(
                    ProjectFile::find($project->custom_land_degradation_map_file_id)->path
                );
                $lumUrl = Storage::url(ProjectFile::find($project->land_use_map_file_id)->path);

                $data['land_degradation_map']   = ['custom_map_url' => $ldmUrl];
                $data['land_use_map']           = ['custom_map_url' => $lumUrl];
                $data['land_suitability_map']   = [
                    [
                        'lu_class' => 21,
                        'lu_suitability_map_url' => '',
                    ], [
                        'lu_class' => 34,
                        'lu_suitability_map_url' => '',
                    ]
                ];
            }

            $url = $project->uses_default_lu_classification ?
                $defaultDatasetUrl : $customDatasetUrl;

            $response = null;
            try {
                Log::info('Sending preprocessing request to service.', [
                    'url' => $url,
                    'project' => $project->id
                ]);

                $response = Http::timeout($this->requestTimeout)
                    ->withToken($this->token)
                    ->post($url, $data)
                    ->throw();

                if ($response->ok()) {

                    $data = $response->json();

                    Log::info('Successfully preprocessed and published project.', [
                        'project' => $project->id
                    ]);

                    // TODO: Save the urls within the project.
                    $project->update([
                        'preprocessing_data' => json_encode($data),
                        'status'             => Project::STATUS_PUBLISHED
                    ]);
                }
            } catch (Exception $ex) {
                Log::error('Failed to preprocess project.', [
                    'project' => $project->id,
                    'error' => $ex->getMessage()
                ]);
            }
        }
    }
}
