<?php

namespace App\Utilities\SCIO;

use Cache;
use Exception;
use Http;

class AWSTokenGenerator
{
    protected $baseURI;
    protected $clientID;
    protected $clientSecret;
    protected $cacheKey;
    protected $requestTimeout;
    protected $cacheTtl;

    public function __construct()
    {
        $this->baseURI = env('SCIO_SERVICES_BASE_API_URL');
        $this->cacheKey = 'aws_scio_token';
        $this->requestTimeout = env('REQUEST_TIMEOUT_SECONDS', 10);
        $this->cacheTtl = env('CACHE_TTL_SECONDS', 3600);
    }

    public function getToken()
    {
        if (Cache::has($this->cacheKey)) {
            return Cache::get($this->cacheKey);
        }

        $tokenUrl = $this->baseURI . "/generatetoken_AWS";
        $response = Http::timeout($this->requestTimeout)->get(
            $tokenUrl,
            "client_id=$this->clientID&client_secret=$this->clientSecret"
        );

        if ($response->failed()) {
            throw new Exception('Failed to get token.');
        }

        // Get the response.
        $accessToken = $response->json('access_token');
        $expiresIn = $response->json('expires_in');

        if (empty($accessToken)) {
            throw new Exception('Failed to get access token.');
        }

        if (empty($expiresIn)) {
            throw new Exception('Failed to get token expiration date.');
        }

        if (!empty($accessToken)) {
            $ttl = $this->cacheTtl;
            if (!empty($expiresIn) && $expiresIn > $this->cacheTtl) {
                $ttl = $expiresIn - $this->cacheTtl;
            }
            Cache::put($this->cacheKey, $accessToken, $ttl);
        }

        return $accessToken;
    }
}
