<?php

namespace App\Utilities\SCIO;

use Http;
use Exception;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Support\Facades\Storage;

class LandCoverClassExtractor
{
    /**
     * The project to process.
     */
    protected $project;

    /**
     * For what ROI (polygon) do we need the classes (defaults to project's ROI)
     */
    protected $ROI;

    /**
     * The token to use for the request.
     */
    protected $token;

    /**
     * The maximum time in seconds to allow before the request times out.
     */
    protected $requestTimeout;

    public function __construct(Project $project, $overridePolygon = null)
    {
        $this->project = $project;
        if ($overridePolygon !== null) {
            $this->ROI = $overridePolygon;
        } else {
            $this->ROI = json_decode($project->polygon, true);
        }

        if (empty($project) || empty($this->ROI)) {
            throw new Exception('You can\'t use LandCoverClassExtractor without a valid project or
                undefined ROI');
        }

        $this->token = (new AWSTokenGenerator())->getToken();
        $this->requestTimeout = 60; // in seconds.
    }

    private function preparePayload()
    {

        $data = [
            'project_id' => (string) $this->project->id,
            'ROI' => $this->ROI,
        ];

        if (!$this->project->uses_default_lu_classification) {
            // Use the custom land degradation file if it exists.
            $data['land_degradation_map'] = ['custom_map_url' => 'n/a'];
            if (!empty($this->project->custom_land_degradation_map_file_id)) {
                $ldmUrl = Storage::url(
                    ProjectFile::find($this->project->custom_land_degradation_map_file_id)->path
                );
                $data['land_degradation_map'] = ['custom_map_url' => $ldmUrl];
            }

            // Use the custom land use map file if it exists.
            if (!empty($this->project->land_use_map_file_id)) {
                $lumUrl = Storage::url(
                    ProjectFile::find($this->project->land_use_map_file_id)->path
                );
                $data['land_use_map'] = ['custom_map_url' => $lumUrl];
            }

            // Check if we have any land suitability maps.
            $customClasses = json_decode($this->project->lu_classes);
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
        return $data;
    }

    private function makeRequest($data)
    {
        $endpoint = env('SCIO_LAND_COVER_CLASS_EXTRACTOR_URL', null);

        try {
            if ($endpoint === null) {
                throw new Exception('SCIO_LAND_COVER_CLASS_EXTRACTOR_URL is not defined');
            }

            $response = Http::timeout($this->requestTimeout)
                ->withToken($this->token)
                ->asJson()
                ->post($endpoint, $data)
                ->throw();

            if ($response->ok()) {
                return $response->json();
            } else {
                throw new Exception('External service request error');
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function extractClasses()
    {
        return $this->makeRequest($this->preparePayload());
    }
}
