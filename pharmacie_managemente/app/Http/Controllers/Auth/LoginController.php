<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoginRequest;
use Illuminate\Support\Facades\Auth;

// use Illuminate\Http\Request;

class LoginController extends Controller
{
    //
    public function index()
    {
        return view('auth.login');
    }

    public function store(StoreLoginRequest $request)
    {
        $validated = $request->validated();
        if (Auth::attempt($validated)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('dashboard.admin');
            } else {
                return redirect()->route('dashboard.employe');
            }
        }

        return back()->withErrors([
            'email'=>'Email ou mode de passe incorrect'
        ]);

    }
}
