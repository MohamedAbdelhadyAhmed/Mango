<?php

namespace App\Http\Controllers\API\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    //
}


// public function login(Request $request)
//     {
//         $request->validate([
//             'email' => 'required|email',
//             'password' => 'required',
//         ]);

//         $admin = Admin::where('email', $request->email)->first();

//         if (!$admin || !Hash::check($request->password, $admin->password)) {
//             throw ValidationException::withMessages([
//                 'email' => ['The provided credentials are incorrect.'],
//             ]);
//         }

//         return response()->json([
//             'admin' => $admin,
//             'token' => $admin->createToken('mobile', ['role:admin'])->plainTextToken
//         ]);

//     }