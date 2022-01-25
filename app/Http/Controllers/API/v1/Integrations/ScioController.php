<?php

namespace App\Http\Controllers\API\v1\Integrations;

use Http;
use Cache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Integrations\GetWocatTechnologiesRequest;
use App\Utilities\SCIO\TokenGenerator;
use App\Http\Requests\Integrations\ListLDNTargetsRequest;
use App\Http\Requests\LandCover\GetLandCoverPercentagesRequest;
use App\Http\Requests\Polygons\GetPolygonsByCoordinatesRequest;
use App\Http\Requests\Polygons\GetAdminLevelAreaPolygonsRequest;
use App\Models\Project;
use App\Utilities\SCIO\WocatTransformer;
use Request;

class ScioController extends Controller
{
    protected $cacheTtl;
    protected $baseURI;
    protected $requestTimeout;
    protected $token;

    public function __construct()
    {
        $generator = new TokenGenerator();
        $this->token = $generator->getToken();
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

    public function getLandCoverPercentages(
        GetLandCoverPercentagesRequest $request,
        Project $project
    ) {
        $mostRecentYear = $request->most_recent_year;
        $country = $project->country_iso_code_3;

        $landCover7Url = data_get(json_decode($project->tif_images), 'land_cover_7');
        $parts = explode('/', $landCover7Url);
        $length = count($parts);

        $identifier = $parts[$length - 2];
        $filename = $parts[$length - 1];

        $response = Http::timeout($this->requestTimeout)
            ->withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->post("$this->baseURI/landcoverPercentagesByROI", [
                'identifier' => $identifier,
                'filename' => $filename,
                'most_recent_year' => $mostRecentYear,
                'country_ISO' => $country,
            ]);

        return response()->json($response->json(), $response->status());
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
}
