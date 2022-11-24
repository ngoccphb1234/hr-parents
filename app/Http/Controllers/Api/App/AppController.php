<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class AppController extends Controller
{
    private string $access_token = 'toenken_fdigfj8g832j2j292';

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

    public function authToken(Request $request){
        $data = $request->all();
        if (!isset($data['app_key']) || !isset($data['app_secret']) || !isset($data['code'])){
            throw new \Exception('khong co key yeu cau');
        }
        $app = Application::query()->where([
            ['app_key', '=', $data['app_key']],
            ['app_secret', '=', $data['app_secret']],
            ['code', '=', $data['code']],
        ])->first();
        if (!$app){
            throw new \Exception('khong co app yeu cau');
        }

        return response()->json(['access_token' => $this->access_token]);
    }


}
