<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResponseMetadata
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $newJson = [
                'timestamp' => now()->toDateTimeString(),
                'ip' => $request->ip()
            ];
            $oldJson = json_decode($response->getContent(), true);
            $statusCode = $response->getStatusCode();

            $newJson['message'] = $oldJson['message'] ?? Response::$statusTexts[$statusCode];

            unset($oldJson['message']);

            if ($statusCode < 400) {
                if (isset($oldJson['data'])) {
                    $newJson = array_merge($newJson, $oldJson);
                } else $newJson['data'] = $oldJson;
            } else {
                $newJson = array_merge($newJson, $oldJson);
            }

            $response->setData($newJson);
        }

        return $response;
    }
}
