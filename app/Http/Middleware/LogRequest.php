<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;

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

        Log::info(sprintf(
            "[IP: %s - USER: %s] | %s %s | STATUS: %s | BODY: %s | RESPONSE: %s",
            $request->ip(),
            $request->user()->getKey(),
            $request->method(),
            $request->path(),
            $response->getStatusCode(),
            $request->getContent(),
            $response->getContent()
        ));

        return $next($request);
    }
}
