<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
          if(!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'admin'
          ]))
          {
            return response()->json([
                'message'=>'invalid admin credential'
            ],401);
          }
          $user = Auth::user();
          $token= $user->createToken('admin-token')->plainTextToken;
          return response()->json([
            'token'=>$token,
            'user' =>$user
          ]);
    }
    
  
}
