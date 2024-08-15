<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    //

    public function login(Request $request)
    {
        // dd($request->all());
        // $request->authenticate();

        // $request->session()->regenerate();

         if(Auth::attempt($request->only('email', 'password')))
         {
            return response()->json([
            'message' => "Successfully logged in",
        ],200);
         }

        
        
    }
    
    public function register(Request $request)
    {
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);
        
        return response()->json([
            'user' => $user
        ],200);
        
    }
}
