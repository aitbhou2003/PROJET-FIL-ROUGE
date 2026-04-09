<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// use Illuminate\Http\Request;

class RegisterController extends Controller
{
    //

    public function index()
    {
        return view('auth.register');
    }


    public function store(StoreRegisterRequest $request)
    {
        $validated = $request->validated();
        User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'is_actif' => $validated['is_actif'] ?? true,
        ]);

        return redirect()->route('register');
    }
}
