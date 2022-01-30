<?php

namespace App\Jobs;

use Http;
use Exception;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
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
        $generator = new AWSTokenGenerator();
        $this->token = $generator->getToken();
        $this->requestTimeout = 30;

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

            Log::info('Gathering data for project ' . $project->id);

            $data =  [
                'project_id'    => $project->id,
                'ROI'           => $project->polygon,
            ];

            // The project uses custom LU classification.
            if (!$project->uses_default_lu_classification) {
                Log::info('Project ' . $project->id . ' uses custom classification');
                $data['land_degradation_map']   = ['custom_map_url' => ''];
                $data['land_use_map']           = ['custom_map_url' => ''];
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

            try {
                $response = Http::timeout($this->requestTimeout)
                    ->withToken($this->token)
                    ->acceptJson()
                    ->asJson()
                    ->post($url, $data);

                if ($response->ok()) {
                    Log::info('Successfully preprocessed and published project ' . $project->id);
                    // TODO: Save the urls within the project.
                    $project->update(['status' => Project::STATUS_PUBLISHED]);
                }
            } catch (Exception $ex) {
                Log::error('Failed to preprocess project ' . $project->id, [
                    'error' => $ex->getMessage()
                ]);
            }
        }
    }
}
