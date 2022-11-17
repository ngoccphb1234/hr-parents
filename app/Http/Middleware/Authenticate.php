<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{

    public function handle($request, Closure $next, ...$guards)
    {

        if (!$request->expectsJson()) {
            if (!$request->user()){
                return redirect()->route('login');
            }
//            $app_key = $request->headers->get(env('APP_SURVEY_KEY'));
//            $request['from_survey_hr'] = false;
//            if ($app_key){
//                if (strcmp(env('APP_SURVEY_SECRET'), $app_key) == 0){
//                    $request['from_survey_hr'] = true;
//                }
//            }
            return $next($request);
        }
//        $app_key = $request->headers->get(env('APP_SURVEY_KEY'));
//        $request['from_survey_hr'] = false;
//        if ($app_key){
//            if (strcmp(env('APP_SURVEY_SECRET'), $app_key) == 0){
//                $request['from_survey_hr'] = true;
//                dd(1);
//            }
//        }
//        return $next($request);
    }
}
