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
    private string $app_key = 'survey-hr';
    private string $app_secret = 'sGFdsivu221hgg';

    public function error()
    {
        return view('error');
    }

    public function login(Request $request)
    {
        $from_survey_hr = $request->get('from_survey_hr');
        if ($from_survey_hr === true && Auth::check()){
            $user = \auth()->user();
            return \redirect('http://hr-survey.local:9000/?user_code=' . $user['user_code']);
        }
        return view('login', ['from_survey_hr' => $from_survey_hr]);
    }

    public function handleLogin(Request $request)
    {
        try {
            $from_survey_hr = $request->get('fromSurveyHr');
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
                $redis = Redis::connection();
//                $exist_redis_key = $redis->get($user['email']);
                $new_user = [
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'user_code' => $user['user_code'],
                ];
                $redis->set($user['user_code'], json_encode($new_user));
                if ((bool)$from_survey_hr === true) {
                    return \redirect('http://hr-survey.local:9000/?user_code=' . $user['user_code']);
                }
                // Authentication passed...
                return Redirect::route('home')->withSuccess('Logged in!');
            }
            return Redirect::back()->withErrors('Oppes! You have entered invalid credentials');
        } catch (\Exception $e) {
            return Redirect::route('error')->withErrors($e->getMessage());
        }
    }

    public function register(Request $request)
    {
        $from_survey_hr = $request->get('from_survey_hr');
        return view('register', ['from_survey_hr' => $from_survey_hr]);
    }

    public function handleRegister(Request $request)
    {
        DB::beginTransaction();
//        $app_key = 'survey-hr';
//        $app_secret = 'sGFdsivu221hgg';
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
            $data['user_code'] = substr(md5(mt_rand()), 0, 7);
            $user = User::query()->create($data);
            if ($user) {
                Auth::loginUsingId($user->id);
            } else {
                throw new \Exception('user not found has been deleted.');
            }
            if ((bool)$from_survey_hr === true) {
                $url = 'http://hr-survey.local:9000/api/register';

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    $this->app_key => $this->app_secret
                ])
                    ->post($url, $data);
                if ($response->failed()) {
                    throw new \Exception('call api register survey hr failed.');
                }
                $is_redirect_to_survey_hr = true;
            }
            DB::commit();
            $redis = Redis::connection();
            $exist_redis_key = $redis->get($user['user_code']);
            if (!$exist_redis_key) {
                $new_user = [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'user_code' => $user['user_code'],
                ];
                $redis->set($user['user_code'], json_encode($new_user));
            }
            if ($is_redirect_to_survey_hr) {
                return \redirect('http://hr-survey.local:9000/?user_code=' . $user['user_code']);
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

    public function home()
    {

        return view('home');
    }
}
