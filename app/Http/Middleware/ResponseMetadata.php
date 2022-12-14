<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResponseMetadata
{
    const MESSAGE = 'MESSAGE';

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
            $statusCode = $response->getStatusCode();
            $newJson = [
                'datetime' => now()->toDateTimeString(),
                'timestamp' => now()->toTimeString(),
                'ip' => $request->ip(),
                'statusCode' => $statusCode
            ];
            $oldJson = json_decode($response->getContent(), true);

            $newJson['message'] = $oldJson[ResponseMetadata::MESSAGE] ?? Response::$statusTexts[$statusCode];

            unset($oldJson[ResponseMetadata::MESSAGE]);

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
