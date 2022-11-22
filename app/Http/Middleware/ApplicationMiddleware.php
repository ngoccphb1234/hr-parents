<?php

namespace App\Http\Middleware;

use App\Models\Application;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;

class ApplicationMiddleware
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
        $queryStrings = $request->query();
        $from_survey_hr = false;
        if (count($queryStrings) > 0) {

            if (isset($queryStrings['app_key']) && isset($queryStrings['app_secret'])) {
                $app = Application::query()->where([
                    ['app_key', '=', $queryStrings['app_key']],
                    ['app_secret', '=', $queryStrings['app_secret']],
                ])->first();
                if (!$app){
                    return Redirect::back();
                }
                $redis = Redis::connection();
                $redis->set($app['app_key'], $app['code']);
                $from_survey_hr = true;
            }
        }
        $request['from_survey_hr'] = $from_survey_hr;
        return $next($request);
//        $queryString = $request->query('code');
////        $app_key = 'survey-hr';
////        $app_secret = 'sGFdsivu221hgg';
//        $from_survey_hr = false;
//        if ($queryString) {
//            if (is_string($queryString)){
//                $check_app = Application::query()->where('code', '=', $queryString)->first();
//                if (!$check_app){
//                    return Redirect::back();
//                }
//                $from_survey_hr = true;
//            }
//        }
//        $request['from_survey_hr'] = $from_survey_hr;
//        return $next($request);
    }
}
