<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
         $Validator=Validator::make($request->all(),[
            'name'=>'required|string',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6',
             'role'=>'required|in:admin,customer',
            'mobile_no' => 'required|digits:10',
            'city'      => 'nullable|string|max:100',
            'address'   => 'nullable|string|max:255',
            'pincode'   => 'required|digits:6',

         ]);
         if($Validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $Validator->errors(),
            ],422);
         } 
         $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>$request->role,
            'mobile_no'=>$request->mobile_no,
            'city'=>$request->city,
            'address'=>$request->address,
            'pincode'=>$request->pincode
         ]);
         return response()->json([
                'status' =>true,
                'message'=>'customer register successfully',
                'user'=>$user
         ]);
    }
    public function login(Request $request)
    {
        if(!Auth::attempt([
            'email' => $request->email, 
            'password' => $request->password,
            'role'=>'customer'
            ]))
            {
                return response()->json([
                    'message'=>'invalid customer credential'
                ],401);
            }
            $user=Auth::user();
            $token=$user->createToken('customer-token')->plainTextToken;
            return response()->json([
                'token'=>$token,
                'user' =>$user
            ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status'=>true,
            'message'=>'logout successfuly' 
        ]);
    }
    public function profile(Request $request,$id)
    {
        $profile=User::where('id',$id)->where('id',auth()->id())->first();
        if(!$profile)
            {
                return response()->json([
                    'status'=>false,
                    'message'=>'no profile fetched'
                ]);
            }
            return response()->json([
                'status'=>true,
                'message'=>'fetch profile',
                'profile'=> $profile
            ]);
    }
}
