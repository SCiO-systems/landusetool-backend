<?php

namespace App\Http\Controllers\API\v1\Integrations;

use Http;
use Cache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Integrations\ListLDNTargetsRequest;

class ScioController extends Controller
{
    protected $cacheTtl;
    protected $baseURI;
    protected $requestTimeout;

    public function __construct()
    {
        $this->cacheTtl = env('CACHE_TTL_SECONDS');
        $this->baseURI = env('SCIO_SERVICES_BASE_API_URL');
        $this->requestTimeout = env('REQUEST_TIMEOUT_SECONDS', 10);
    }

    public function listLDNTargets(ListLDNTargetsRequest $request)
    {
        $country = $request->country;
        $cacheKey = "ldn_targets_$country";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::timeout($this->requestTimeout)
            ->retry(3, 300)
            ->acceptJson()
            ->asJson()
            ->get("$this->baseURI/getLDNTargets/$country");

        $status = $response->json('code');
        $content = $response->json('response');

        if ($response->ok()) {
            Cache::put($cacheKey, $content, $this->cacheTtl);
        }

        return response()->json($content, $status);
    }
}
