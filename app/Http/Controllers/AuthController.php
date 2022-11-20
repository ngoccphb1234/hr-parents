<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function login()
    {
        return view('login');
    }

    public function handleLogin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                // Authentication passed...
                return Redirect::route('home')->withSuccess('Logged in!');
            }
            return Redirect::route('home')->withErrors('Oppes! You have entered invalid credentials');
        } catch (\Exception $e) {
            return Redirect::route('login')->withErrors($e->getMessage());
        }
    }

    public function register(Request $request)
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

        return view('register', ['from_survey_hr' => $from_survey_hr]);
    }

    public function handleRegister(Request $request)
    {
        DB::beginTransaction();
        $app_key = 'survey-hr';
        $app_secret = 'sGFdsivu221hgg';
        $from_survey_hr = $request->get('fromSurveyHr');
        //luu thong tin vao db hrPro
        //call Api sang surveyHR de dang ki tai khoan
        // redirect va login vao surveyHR
        try {
            request()->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);
            $is_redirect_to_survey_hr = false;
            $data = $request->all();
            $data['password'] = Hash::make($data['password']);
            $user = User::query()->create($data);
            if ($user) {
                Auth::login($user);
            }
            if ($from_survey_hr == true) {
                $url = 'http://127.0.0.1:8001/api/register';

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    $app_key => $app_secret
                ])
                    ->post($url, $request->all());
                if ($response->failed()){
                    throw new \Exception('call api register survey hr failed.');
                }
                $is_redirect_to_survey_hr = true;
            }
            DB::commit();
            $redis = Redis::connection();
            $exist_redis_key = $redis->get($user['email']);
            if (!$exist_redis_key){
                $redis->set($user['email'], $user);
            }
            if ($is_redirect_to_survey_hr){
                return \redirect('http://localhost:8001?user_email='.$user['email']);
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
        $redis = Redis::connection();
        dd($redis->get('hallo'));
//        $get = $redis->get('ngoc');
        dd($redis->keys('*'));
        return view('info', ['user2' => '\ss']);
    }

    public function home()
    {

        return view('home');
    }
}
