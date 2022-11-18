<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
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
        $app_key = env('APP_SURVEY_KEY');
        $app_secret = env('APP_SURVEY_SECRET');
        $from_survey_hr = false;
        if (count($queryStrings) > 0) {
            if (!$app_key || !$app_secret) {
                abort(403);
            }
            $secret_value = $queryStrings[$app_key];
            if (!$secret_value) {
                return Redirect::route('home');
            }

            if (strcmp($secret_value, env('APP_SURVEY_SECRET')) != 0) {
                return Redirect::route('home');
            }
            $from_survey_hr = true;
        }

        return view('register', ['from_survey_hr' => $from_survey_hr]);
    }

    public function handleRegister(Request $request)
    {
        dd($request->all());
        //luu thong tin vao db hrPro
        //call Api sang surveyHR de dang ki tai khoan
        // redirect va login vao surveyHR
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
                Auth::login($user);
            }

            return Redirect::route('home')->withSuccess('Great! You have Successfully Register');
        } catch (\Exception $e) {
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
