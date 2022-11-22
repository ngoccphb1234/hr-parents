<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class AppController extends Controller
{

    public function authApp(Request $request)
    {
        try {
            $data = $request->validate([
                'app_key' => 'required',
                'app_secret' => 'required',
            ]);
            $app = Application::query()->where([
                ['app_key', '=', $data['app_key']],
                ['app_secret', '=', $data['app_secret']],
            ])->first();
            if (!$app){
                return response()->json('app not found');
            }
            return response()->json($app->code);
        } catch (\Exception $e) {
            return response()->json($e);

        }
    }
}
