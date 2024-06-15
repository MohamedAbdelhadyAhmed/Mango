<?php

namespace App\Http\Controllers\API\Auth\User;

use App\Http\Controllers\Controller;
// use App\Http\Traits\Api;
use App\Mail\MangoMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // use Api;

    //
    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->where('status', 1)->first();
        //  return $user;
        if ($user) {

            if (!$user || !Hash::check($request->password, $user->password)) {
                // return $this->error_message('code is invalid', ["credentials" => ""]);
                return response()->json([
                    'status' => true,
                    'message' => 'The provided credentials are incorrect',
                    'data' => [],

                ]);
            }

            // $token = "Bearer " . $user->createToken($request->email)->plainTextToken;
            $token = "Bearer " . $user->createToken('user-token', ['role:user'])->plainTextToken;

            $user->token = $token;
            if (is_null($user->email_verified_at)) {
                // return $this->data(compact('user'), 'User is not verified ');
                // return $this->data(compact('user'), 'User Created Successfully');
                $data = array('data' => $user);

                return response()->json([
                    'status' => true,
                    'message' => 'User is not verified',
                    'data' => $data,

                ]);
            }
            // return $this->data(compact('user'), 'User Logged In Successfully');
            $data = array('data' => $user);
            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'data' => $data,

            ]);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'User  not found',
                'data' => [],

            ]);

        }

    }
    public function Logout(Request $request)
    {
        $token = $request->header('Authorization');
        $auth_user = Auth::guard('sanctum')->user();
        $BearerWithId = explode('|', $token)[0];
        $TokenId = explode(' ', $BearerWithId)[1];
        $auth_user->tokens()->where('id', $TokenId)->delete();
        // return $this->success_message("User Logged Out Successfuly");
        return response()->json([
            'status' => true,
            'message' => 'User Logged Out Successfuly',
            'data' => [],

        ]);

    }
    public function LogoutFromAll()
    {
        $auth_user = Auth::guard('sanctum')->user();
        $auth_user->tokens()->delete();
        // return $this->success_message("User Logged Out Successfuly From All Devices");
        return response()->json([
            'status' => true,
            'message' => 'User Logged Out Successfuly From All Devices',
            'data' => [],

        ]);

    }
    public function ChangePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|min:8',
            'new_password' => 'required|min:8|confirmed',
        ]);
        $auth_user = Auth::guard('sanctum')->user();

        if (!Hash::check($request->current_password, $auth_user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match our records.'],
            ]);
        }

        $auth_user->password = Hash::make($request->new_password);
        $auth_user->save();
        // return $this->success_message("Password changed successfully.");
        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully.',
            'data' => [],

        ]);
    }
    public function ResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $user = User::where('email', $request->email)->where('status', 1)->first();
        // $user = User::find($request->email);
        // dd($user);
        if ($user) {
            // crearte code
            $code = rand(100000, 999999);
            $user->code = $code;
            $user->save();
            $details = [
                'name' => $request->first_name . ' ' . $request->last_name . 'Your Code Is : ' . $code,
                'email' => $request->email,
            ];

            Mail::to($details['email'])->send(new MangoMail($details));

            // send code to email
            //  return  $user
            // return user date with code
            // return $this->data(compact('user'), 'code send Successfuly ');
            $data = array('data' => $user);

            return response()->json([
                'status' => true,
                'message' => 'code send Successfuly',
                'data' => $data,

            ]);
        } else {
            // return $this->data([], 'User not Found');
            return response()->json([
                'status' => true,
                'message' => 'User not Found',
                'data' => [],

            ]);
        }
        // send code  - check cod is corect - form to enter new password

    }
    public function ResetPasswordCheckCode(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'code' => 'required|exists:users,code',
        ]);
        $user = User::where('email', $request->email)->first();

        if ($user->code == $request->code) {
            $user->email_verified_at = date('Y-m-dH:i:s');
            // $user->status = 1;
            $user->save();
            // return $this->data(compact('user'), 'code is valid');
            $data = array('data' => $user);
            return response()->json([
                'status' => true,
                'message' => 'code is valid',
                'data' => $data,

            ]);
        } else {
            // return $this->error_message('code is invalid', ["code" => "code is invalid"]);
            return response()->json([
                'status' => true,
                'message' => ' code is invalid',
                'data' => [],

            ]);

        }
        //  return  $user;

        // return user date with code
        // return $this->data(compact('user'), );
    }
    public function ResetPasswordChangePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
        $user = User::where('email', $request->email)->where('status', 1)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            // return $this->success_message("Password changed successfully.");
            // return $this->data(compact('user'), 'Password changed successfully.');
            $data = array('data' => $user);
            return response()->json([
                'status' => true,
                'message' => 'Password changed successfully',
                'data' => $data,

            ]);

        } else {

            return response()->json([
                'status' => true,
                'message' => 'user not found',
                'data' => [],

            ]);

        }

    }
}
