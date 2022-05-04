<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Http\Request;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add the fields to remove from the logs.
        $body = json_encode($request->except(['password']));
        $user = $request->user() !== null ? $request->user()->getKey() : 'GUEST';

        Log::info(sprintf(
            "[IP: %s - USER: %s] | %s %s | BODY: %s | QS: %s | RESPONSE %s %s",
            $request->ip(),
            $user,
            $request->method(),
            $request->path(),
            $body,
            $request->getQueryString(),
            $response->getStatusCode(),
            $response->getContent(),
        ));

        return $response;
    }
}
