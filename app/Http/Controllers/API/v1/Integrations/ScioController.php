<?php

namespace App\Http\Controllers\API\v1\Integrations;

use Http;
use Cache;
use App\Http\Controllers\Controller;
use App\Utilities\SCIO\TokenGenerator;
use App\Http\Requests\Integrations\ListLDNTargetsRequest;
use App\Http\Requests\Polygons\GetPolygonsByCoordinatesRequest;
use App\Http\Requests\Polygons\GetAdminLevelAreaPolygonsRequest;

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
    public function listLDNTargets(ListLDNTargetsRequest $request)
    {
        $country = $request->country_iso_code_3;
        $cacheKey = "ldn_targets_$country";

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
            ->post("$this->baseURI/getGDAMPolygonFeatureByPoint", [
                'admin_level' => $adminLevel,
                'resolution' => 'low',
                'point' => $point,
            ]);

        $polygon = $response->json();

        return response()->json($polygon, $response->status());
    }
}
