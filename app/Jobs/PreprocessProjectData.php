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
                'ROI' => json_decode($project->polygon, true),
                'ROI_file_url' => null,
            ];

            // If there is a roi_file_id append it to data
            if (!empty($project->roi_file_id)) {
                $roiFileUrl = Storage::url(
                    ProjectFile::find($project->roi_file_id)->path
                );
                $data['ROI_file_url'] = $roiFileUrl;
            }

            // The project uses custom LU classification.
            if (!$project->uses_default_lu_classification) {
                Log::info('Project ' . $project->id . ' uses custom classification.');

                // Use the custom land degradation file if it exists.
                $data['land_degradation_map'] = ['custom_map_url' => 'n/a'];
                if (!empty($project->custom_land_degradation_map_file_id)) {
                    $ldmUrl = Storage::url(
                        ProjectFile::find($project->custom_land_degradation_map_file_id)->path
                    );
                    $data['land_degradation_map'] = ['custom_map_url' => $ldmUrl];
                }

                // Use the custom land use map file if it exists.
                if (!empty($project->land_use_map_file_id)) {
                    $lumUrl = Storage::url(
                        ProjectFile::find($project->land_use_map_file_id)->path
                    );
                    $data['land_use_map'] = ['custom_map_url' => $lumUrl];
                }

                // Check if we have any land suitability maps.
                $customClasses = json_decode($project->lu_classes);
                foreach ($customClasses as $luClass) {
                    if (!empty($luClass->file_id)) {
                        $lsmUrl = Storage::url(ProjectFile::find($luClass->file_id)->path);

                        $data['land_suitability_map'][] = [
                            'lu_class' => $luClass->value,
                            'lu_suitability_map_url' => $lsmUrl,
                        ];
                    }
                }
            }

            $url = $project->uses_default_lu_classification ?
                $defaultDatasetUrl : $customDatasetUrl;

            try {
                Log::info('Sending preprocessing request to service.', [
                    'url' => $url,
                    'project' => $project->id
                ]);

                $response = Http::timeout($this->requestTimeout)
                    ->withToken($this->token)
                    ->asJson()
                    ->post($url, $data)
                    ->throw();

                if ($response->ok()) {

                    // The response we got back from the service.
                    $preprocessingData = $response->json();

                    // Send the polygon in order to get back the total ROI area in hectares.
                    $response = Http::timeout($this->requestTimeout)
                        ->withToken($this->token)
                        ->acceptJson()
                        ->asJson()
                        ->post(env('SCIO_CUSTOM_ROI_HECTARES_SERVICE_URL'), [
                            'project_id' => (string) $project->id,
                            'ROI' => $data['ROI'],
                            'ROI_file_url' => $data['ROI_file_url'],
                            'polygon' => $data['ROI'],
                            'polygon_url' => $data['ROI_file_url'],
                        ]);

                    // Get the ROI area in hectares.
                    $preprocessingData['total_roi_area'] = $response->json();

                    $project->update([
                        'preprocessing_data' => json_encode($preprocessingData),
                        'status'             => Project::STATUS_PUBLISHED
                    ]);

                    Log::info('Successfully preprocessed and published project.', [
                        'project' => $project->id
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
