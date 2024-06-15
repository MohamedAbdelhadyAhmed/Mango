<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
// use App\Http\Traits\Api;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    // use Api;
    //
    public function SendCode(Request $request)
    {
        $token = $request->header('Authorization');
        $auth_user = Auth::guard('sanctum')->user();
        //    dd(   $auth_user->email );
        // crearte code
        $code = rand(10000, 99999);
        $user = User::find($auth_user->id);
        $user->code = $code;
        $user->save();
        $user->token = $token;
        //  return  $user;

        // return user date with code
        $data = array('data' => $user);

        // return $this->data(compact('user'), );
        return response()->json([
            'status' => true,
            'message' => 'code sent successfully',
            'data' => $data,

        ]);
    }
    public function CheckCode(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:users,code',
        ]);
        $token = $request->header('Authorization');
        $auth_user = Auth::guard('sanctum')->user();
        // return   $auth_user;

        //    dd(   $auth_user->email );
        // crearte code
        // return $auth_user;
        $user = User::find($auth_user->id);
        if ($user->code == $request->code) {
            $user->email_verified_at = date('Y-m-dH:i:s');
            $user->status = 1;
            $user->save();
            $user->token = $token;
            // return $this->data(compact('user'));
            $data = array('data' => $user);

            // return $this->data(compact('user'), );
            return response()->json([
                'status' => true,
                'message' => 'code is valid',
                'data' => $data,

            ]);
        } else {
            // return $this->error_message( 'code is invalid',["code" => "code is invalid"]);
            return response()->json([
                'status' => true,
                'message' => 'code is invalid',
                'data' => [],

            ]);

        }
        //  return  $user;
        // return user date with code
        // return $this->data(compact('user'), );

    }
}
