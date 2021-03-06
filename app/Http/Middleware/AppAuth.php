<?php

namespace App\Http\Middleware;

use Closure;


use Illuminate\Support\Facades\Log;

class AppAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api_token = env('api_token');
        $header = $request->header('Authorization');
        $array = explode(' ', $header);
        $api_token_in = $array[1];

        if($api_token_in != $api_token) {
            return Response('forbidden', 403);
        }

        return $next($request);
    }
}
