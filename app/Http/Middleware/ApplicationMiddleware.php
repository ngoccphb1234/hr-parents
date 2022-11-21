<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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
        $app_key = 'survey-hr';
        $app_secret = 'sGFdsivu221hgg';
        $from_survey_hr = false;
        if (count($queryStrings) > 0) {
            if (!$app_key || !$app_secret) {
                abort(403);
            }
            $secret_value = null;
            if (isset($queryStrings[$app_key])) {
                $secret_value = $queryStrings[$app_key];
            }
            if (!$secret_value) {
                return Redirect::route('home');
            }

            if (strcmp($secret_value, $app_secret) != 0) {
                return Redirect::route('home');
            }
            $from_survey_hr = true;
        }
        $request['from_survey_hr'] = $from_survey_hr;
        return $next($request);
    }
}
