<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class PrjAuth extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $responseData = [
            "errors"  => null,
            "success" => null,
        ];
        try {
            $requestToken = $request->get('api_token');
            $apiToken = config('app.api_token');
            if (!$apiToken) {
                throw new \Exception("API token isn't set in system");
            }

            if (!$requestToken or $requestToken !== $apiToken) {
                return response('', 403);
            }

            return $next($request);
        }
        catch (\Exception $Exc) {
            $responseData["success"] = false;
            $responseData["errors"] = config("app.debug") ? "{$Exc->getMessage()}" : "Some error occurred";
            throw new HttpResponseException(response()->json($responseData, 500));
        }
    }
}
