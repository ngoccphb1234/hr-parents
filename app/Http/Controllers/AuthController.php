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

    public function register()
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
            if ($user){
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
