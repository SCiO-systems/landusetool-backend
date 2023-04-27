<?php

namespace App\Http\Controllers\API\v1\Integrations;

use Log;
use Http;
use Cache;
use Storage;
use Exception;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Http\Controllers\Controller;
use App\Utilities\SCIO\TokenGenerator;
use App\Utilities\SCIO\WocatTransformer;
use App\Utilities\SCIO\AWSTokenGenerator;
use App\Http\Requests\Integrations\PrepareLDNMapRequest;
use App\Http\Requests\Integrations\ListLDNTargetsRequest;
use App\Http\Requests\Integrations\CalculateHectaresRequest;
use App\Http\Requests\Integrations\GetWocatTechnologyRequest;
use App\Http\Requests\Integrations\GetWocatTechnologiesRequest;
use App\Http\Requests\Polygons\GetPolygonsByCoordinatesRequest;
use App\Http\Requests\Polygons\GetAdminLevelAreaPolygonsRequest;

class ScioController extends Controller
{
    protected $cacheTtl;
    protected $baseURI;
    protected $requestTimeout;
    protected $token;
    protected $lambdaToken;

    public function __construct()
    {
        $this->token = (new TokenGenerator())->getToken();
        $this->lambdaToken = (new AWSTokenGenerator())->getToken();
        $this->cacheTtl = env('CACHE_TTL_SECONDS', 3600);
        $this->baseURI = env('SCIO_SERVICES_BASE_API_URL', '');
        $this->requestTimeout = env('REQUEST_TIMEOUT_SECONDS', 10);
    }

    /**
     * List the LDN targets for a given country.
     *
     * @param ListLDNTargetsRequest $request
     * @return void
     */
    public function getCountryLevelLinks(ListLDNTargetsRequest $request)
    {
        $country = $request->country_iso_code_3;
        $cacheKey = "country_level_links_$country";

        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        $response = Http::timeout($this->requestTimeout)
            ->acceptJson()
            ->withToken($this->token)
            ->asJson()
            ->get("$this->baseURI/getLDNTargets/$country");

        $content = $response->json('response');

        if ($response->ok()) {
            Cache::put($cacheKey, $content, $this->cacheTtl);
        }

        return response()->json($content, $response->status());
    }


    /**
     * Get the polygons for a given admin level area.
     *
     * @param GetAdminLevelAreaPolygonsRequest $request
     * @return void
     */
    public function getAdminLevelAreaPolygons(GetAdminLevelAreaPolygonsRequest $request)
    {
        $country = $request->country_iso_code_3;
        $adminLevel = $request->administrative_level;
        $cacheKey = "admin_level_area_polygons_$country$adminLevel";

        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        $response = Http::timeout($this->requestTimeout)
            ->withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->post("$this->baseURI/getGADMPolygonFeatureCollection", [
                'country' => $country,
                'admin_level' => $adminLevel,
                'resolution' => 'low'
            ]);

        $coordinates = $response->json();

        if ($response->ok()) {
            Cache::put($cacheKey, $coordinates, $this->cacheTtl);
        }

        return response()->json($coordinates, $response->status());
    }

    /**
     * Get the polygons for the given coordinates.
     *
     * @param GetPolygonsByCoordinatesRequest $request
     * @return void
     */
    public function getPolygonsByCoordinates(GetPolygonsByCoordinatesRequest $request)
    {
        $point = $request->point;
        $adminLevel = $request->administrative_level;

        $response = Http::timeout($this->requestTimeout)
            ->withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->post("$this->baseURI/getGADMPolygonFeatureByPoint", [
                'admin_level' => $adminLevel,
                'resolution' => 'low',
                'point' => $point,
            ]);

        $polygon = $response->json();

        return response()->json($polygon, $response->status());
    }

    public function getWocatTechnologies(GetWocatTechnologiesRequest $request)
    {
        $response = Http::timeout($this->requestTimeout)
            ->withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->post("$this->baseURI/technologies", [
                'keyword' => $request->keyword,
                'operation' => [
                    'action' => 'search',
                    'details' => [
                        'from' => $request->from,
                        'size' => $request->size,
                    ],
                ],
            ]);

        $items = [];
        $total = $response->json('response.total');

        if ($response->ok()) {
            $data = $response->json('response.data');
            $items = (new WocatTransformer($data))->getTransformedOutput();
        }

        return response()->json(
            ['data' => ['items' => $items, 'total' => $total]],
            $response->status(),
            [],
            JSON_UNESCAPED_SLASHES
        );
    }

    public function getWocatTechnology(GetWocatTechnologyRequest $request, $techId)
    {
        $response = Http::timeout($this->requestTimeout)
            ->withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->post("$this->baseURI/technology", [
                'id' => $techId,
                'alias' => 'wocat_technologies',
            ]);

        $item = null;

        if ($response->ok()) {
            $data = [$response->json('data.response')];
            $item = (new WocatTransformer($data))->getTransformedOutput()[0];
        }

        return response()->json(
            ['data' => ['item' => $item]],
            $response->status(),
            [],
            JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Get the intersecting areas between the ROI and another polygon.
     *
     * @param CalculateHectaresRequest $request
     * @param Project $project
     * @return void
     */
    public function getIntersectingArea(CalculateHectaresRequest $request, Project $project)
    {
        $roiPolygon = json_decode($project->polygon);
        $polygonFile = ProjectFile::find($request->polygon_file_id);

        if (empty($polygonFile)) {
            return response()->json(['message' => 'Polygon file not found.'], 404);
        }

        $polygonFileContents = Storage::get($polygonFile->path);

        // If there is a roi_file_id append it to data
        $roiFileUrl = null;
        if (!empty($project->roi_file_id)) {
            $roiFileUrl = Storage::url(
                ProjectFile::find($project->roi_file_id)->path
            );
        }

        $response = Http::timeout($this->requestTimeout)
            ->withToken($this->lambdaToken)
            ->acceptJson()
            ->asJson()
            ->post(env('SCIO_CUSTOM_ROI_HECTARES_SERVICE_URL'), [
                'project_id' => (string) $project->id,
                'ROI' => $roiPolygon,
                'ROI_file_url' => $roiFileUrl,
                'polygon' => json_decode($polygonFileContents),
            ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to get hectares of intersecting area.'], 500);
        }

        $hectares = $response->json();

        if (empty($hectares)) {
            return response()->json(['hectares' => 0]);
        }

        return response()->json(['hectares' => $hectares]);
    }

    /**
     * Prepares the LDN Map
     */
    public function prepareLDNMap(PrepareLDNMapRequest $request, Project $project)
    {
        $cacheKey = $project->id . '_ldn_map';

        if (Cache::has($cacheKey)) {
            return response()->json([
                'data' => ['ldn_map' => Cache::get($cacheKey)]
            ]);
        }

        // If there is a roi_file_id append it to data
        $roiFileUrl = null;
        if (!empty($project->roi_file_id)) {
            $roiFileUrl = Storage::url(
                ProjectFile::find($project->roi_file_id)->path
            );
        }

        $polygons_list = [];
        if (!empty($request->polygons_list)) {
            $polygons_list = collect($request->polygons_list)->map(function ($elm) {
                $url = Storage::url(
                    ProjectFile::find($elm['file_id'])->path
                );
                return [
                    'value' => $elm['value'],
                    'polygon' => null,
                    'polygon_url' => $url,
                ];
            })->filter(function ($elm) {
                return $elm['polygon_url'] !== null;
            })->toArray();
        }

        $preprocessedData = json_decode($project->preprocessing_data);

        try {
            $response = Http::timeout($this->requestTimeout)
                ->withToken($this->lambdaToken)
                ->acceptJson()
                ->asJson()
                ->post(env('SCIO_CUSTOM_LDNMAP_SERVICE_URL'), [
                    'project_id' => (string) $project->id,
                    'ROI' => json_decode($project->polygon, true),
                    'ROI_file_url' => $roiFileUrl,
                    'polygons_list' => $polygons_list,
                    'land_degradation' => $preprocessedData->land_degradation,
                ])
                ->throw();

            $data = $response->json();
            if ($response->ok() && array_key_exists('ldn_map', $data)) {
                if ($response->ok()) {
                    Cache::put($cacheKey, $data['ldn_map'], $this->cacheTtl);
                }

                return response()->json([
                    'data' => ['ldn_map' => $data['ldn_map']]
                ]);
            } else {
                throw new Exception('LDN map link was not found in the response');
            }
        } catch (Exception $ex) {
            Log::error('Failed to prepare LDN map with the given input.', [
                'project_id' => (string) $project->id,
                'ROI' => json_decode($project->polygon, true),
                'ROI_file_url' => $roiFileUrl,
                'polygons_list' => $polygons_list,
                'error' => $ex->getMessage()
            ]);
        }

        return response()->json(['message' => 'Failed to generate LDN map with the given input.'], 500);
    }

    public function getEconWocatTechnology(GetWocatTechnologyRequest $request, $techId)
    {
        $response = Http::timeout($this->requestTimeout)
            ->withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->get("$this->baseURI/econwocat/$techId");

        if ($response->ok()) {
            $data = (object) $response->json('data.0._source');
        }

        return response()->json(
            ['data' => $data],
            $response->status(),
            [],
            JSON_UNESCAPED_SLASHES
        );
    }
}
