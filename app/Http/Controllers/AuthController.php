<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use App\Models\UserCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{


    public function error()
    {
        return view('error');
    }

    public function login(Request $request)
    {

        return view('login');
    }

    public function handleLogin(Request $request)
    {
        try {

//            $from_survey_hr = $request->get('fromSurveyHr');
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = User::query()->where('email', '=', $credentials['email'])->first();
                if (!$user) {
                    throw new \Exception('user not found has been deleted.');
                }
                // Authentication passed...
                Auth::login($user);
                return Redirect::route('home');

            }
            return Redirect::back()->withErrors('Oppes! You have entered invalid credentials');
        } catch (\Exception $e) {
            return Redirect::route('error')->withErrors($e->getMessage());
        }
    }

    public function register(Request $request)
    {
        return view('register');
    }

    public function handleRegister(Request $request)
    {

        try {
            request()->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);
            $data = $request->all();
            $data['password'] = Hash::make($data['password']);
            $user = User::query()->create($data);
            if ($user) {
                $code = substr(md5(mt_rand()), 0, 7);

                $userCode = new UserCode([['user_id' => $user['id']],'code' => $code]);

                $user->userCode()->save($userCode);
                Auth::loginUsingId($user->id);
            } else {
                throw new \Exception('user not found has been deleted.');
            }
            return Redirect::route('home')->withSuccess('Great! You have Successfully Register');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('error')->withErrors($e->getMessage());
        }
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return Redirect::route('home');
    }

    public function info()
    {
        return view('info');
    }

    public function home(Request $request)
    {
//        $app_key = $request->get('app_key');
//        if ($app_key){
//            $application = Application::query()->where('app_key', '=', $app_key)->first();
//            if ($application){
//                return \redirect()->to($application->url_callback.'?code='.$application->code);
//            }
//        }
        return view('home');
    }

//    public function authUser(Request $request){
//        $get_access_token = $request->get('access_token');
//        if (!$get_access_token && $get_access_token != $this->access_token){
//            throw new \Exception('khong co quyen truy cap');
//        }
//        //tra ve user
//        if (!Auth::check()){
//            return Redirect::route('home');
//        }
//        return response()->json(\auth()->user());
//    }

//    public function authCallback(Request $request){
//        $app_secret = $request->get('key_secret');
//        if (!$app_secret){
//            return Redirect::route('home');
//        }
//
//        $response = Http::post( 'http://survey.hrpro.local:9000/login-by-hrpro', [
//            'app_code' => $this->app_code,
//            'app_secret' => $app_secret,
//        ]);
//        dd($response);
//
//        if ($response->failed()) {
//            throw new \Exception('Co loi khi goi api hrpro');
//        }
//        return Redirect::to('http://survey.hrpro.local:9000');
//    }

//    public function callToSurveyHR(Request $request){
//
////        if (!Auth::check()){
////            return Redirect::route('home');
////        }
//        $response = Http::post( 'http://survey.hrpro.local:9000/api/auth-by-hrpro', [
//            'app_code' => $this->app_code,
//            'app_secret' => $this->app_secret,
//        ]);
//        if ($response->failed()) {
//            throw new \Exception('Co loi khi goi api hrpro');
//        }
//        if (!Auth::check()){
//            return Redirect::route('home');
//        }
//        dd($response->json());
//    }

    public function authorizeApp(Request $request)
    {
        $app_key = $request->get('app_key');

        if ($app_key) {
            $application = Application::query()->where('app_key', '=', $app_key)->first();
            if ($application && Auth::check()) {
                $user_code = UserCode::query()->where('user_id', '=', Auth::id())->first();
                if (!$user_code){
                    throw new \Exception('ko co user code');
                }
                return \redirect()->to($application->url_callback . '?code=' . $user_code['code']);
            }
        }
        throw new \Exception('ko co app key');
    }


}


