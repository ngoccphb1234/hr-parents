<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use App\Models\UserCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class AppController extends Controller
{

//    public function authApp(Request $request)
//    {
//        try {
//            $data = $request->validate([
//                'app_key' => 'required',
//                'app_secret' => 'required',
//            ]);
//            $app = Application::query()->where([
//                ['app_key', '=', $data['app_key']],
//                ['app_secret', '=', $data['app_secret']],
//            ])->first();
//            if (!$app){
//                return response()->json('app not found');
//            }
//            return response()->json($app->code);
//        } catch (\Exception $e) {
//            return response()->json($e);
//
//        }
//    }
//
    public function authUser(Request $request){

        $data = $request->all();
        if (!isset($data['app_key']) || !isset($data['app_secret']) || !isset($data['code'])){
            throw new \Exception('khong co key yeu cau');
        }

        $app = Application::query()->where([
            ['app_key', '=', $data['app_key']],
            ['app_secret', '=', $data['app_secret']],
        ])->first();

        //xu li code
        if (!$app){

            throw new \Exception('khong co app yeu cau');
        }
        $user_code = UserCode::query()->where('code', '=', $data['code'])->first();
        if (!$user_code){
            throw new \Exception('khong co user code');
        }
        $user = User::query()->where('id', '=', $user_code['user_id'])->first();
        if ($user){
            return response()->json($user);
        }
        throw new \Exception('user not found!');
    }


}
