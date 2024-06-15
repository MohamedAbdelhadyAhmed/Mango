<?php

namespace App\Http\Controllers\API\Auth\User;

use App\Http\Controllers\Controller;
// use App\Http\Traits\Api;
use App\Mail\MangoMail;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    //
    // use Api;
    public function CreateUser(Request $request)
    {
        $user_data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        // dd($user);
        if ($user == null) {
            // dd("found 1 ");
            $user_phone = User::where('phone', $request->phone)->first();
            if ($user_phone == null) {
                $user_data['password'] = Hash::make($request->password);
                $new_user = User::create($user_data);
                $code = rand(100000, 999999);
                $new_user->code = $code;
                $new_user->save();
                $details = [
                    'name' => $request->first_name . ' ' . $request->last_name . 'Your Code Is : ' . $code,
                    'email' => $request->email,
                ];

                Mail::to($details['email'])->send(new MangoMail($details));

                $token = "Bearer " . $new_user->createToken('user-token', ['role:user'])->plainTextToken;
                $new_user->token = $token;

                $data = User::find($new_user->id);
                $data->token = $token;
                if ($new_user) {
                    return response()->json([
                        'status' => true,
                        'message' => 'User Created Successfully',
                        'data' => compact('data'),
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Something Went Wrong',
                        'data' => [],
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Phone Number must be unique',
                    'data' => [],
                ]);
            } // End User Phone

        } elseif ($user != null && $user->status == 0) {
            // dd("found 2 ");

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->address = $request->address;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $code = rand(100000, 999999);
            $user->code = $code;
            $user->password = Hash::make($request->password);
            $user->save();
            $details = [
                'name' => $request->first_name . ' ' . $request->last_name . 'Your Code Is : ' . $code,
                'email' => $request->email,
            ];

            Mail::to($details['email'])->send(new MangoMail($details));

            $token = "Bearer " . $user->createToken('user-token', ['role:user'])->plainTextToken;
            $user->token = $token;
            // $data = compact('user');
            $data = array('data' => $user);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'data' => $data,
            ]);
        } elseif ($user != null && $user->status == 1) {
            // dd("found 3 ");

            return response()->json([
                'status' => false,
                'message' => 'User already exists and he is verified',
                'data' => [],
            ]);
        }
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
            $user->status = 1;
            $user->update(['status' => $user->status, 'email_verified_at' => date('Y-m-dH:i:s')]);

            // $user->email_verified_at = date('Y-m-dH:i:s');
            // $user->status = 1;
            // $user->save();
            $user->token = $token;
            // return $this->data(compact('user'));
            $data = array('data' => $user);

            return response()->json([
                'status' => true,
                'message' => 'code is wright you can login now',
                'data' => $data,

            ]);
        } else {
            // return $this->error_message( 'code is invalid',["code" => "code is invalid"]);
            return response()->json([
                'status' => false,
                'message' => 'code is invalid',
                'data' => [],

            ]);

        }

    }
    public function UserVote(Request $request)
    {
        $data = $request->validate([
            'facebook_link' => 'required',
            'instagram_link' => 'required',
            'status_in_media' => 'required',
        ]);
        // $token = $request->header('Authorization');
        $auth_user = Auth::guard('sanctum')->user();
        $data['user_id'] = $auth_user->id;
        if ($auth_user->status == 1) {
            $poll = Poll::create($data);
            if ($poll) {
                return response()->json([
                    'status' => true,
                    'message' => 'Poll Created Successfully',
                    'data' => [],

                ]);
            } else {
                // return $this->error_message( 'code is invalid',["code" => "code is invalid"]);
                return response()->json([
                    'status' => false,
                    'message' => 'somthing went Wrong',
                    'data' => [],

                ]);
            }

        } else {
            // return $this->error_message( 'code is invalid',["code" => "code is invalid"]);
            return response()->json([
                'status' => false,
                'message' => 'User  not found',
                'data' => [],

            ]);

        }

    }
    public function UserVoteEdit(Request $request)
    {
        $auth_user = Auth::guard('sanctum')->user();
        $poll = Poll::where('user_id', $auth_user->id)->first();
        if ($poll->status == 'new') {
            $poll->status = 'old';
            $poll->save();

            return response()->json([
                'status' => true,
                'message' => 'Poll Updated Successfully',
                'data' => [],

            ]);

        } else {
            // return $this->error_message( 'code is invalid',["code" => "code is invalid"]);
            return response()->json([
                'status' => false,
                'message' => 'Vote  not found',
                'data' => [],

            ]);

        }

    }

    // public function CreateUser(Request $request)
    // {
    //     $user_data = $request->validate([
    //         'first_name' => 'required|string',
    //         'last_name' => 'required|string',
    //         'address' => 'required|string',
    //         // 'city' => 'required|string',
    //         // 'country' => 'required|string',
    //         'email' => 'required|string|email',
    //         'phone' => 'required',
    //         'password' => 'required|min:8|confirmed',

    //     ]);

    //     $user = User::where('email', $request->email)->first();
    //     if ($user == null) {

    //         $user_data['password'] = Hash::make($request->password);
    //         $new_user = User::create($user_data);
    //         $code = rand(10000, 99999);
    //         // $user = User::find($user->id);
    //         $new_user->code = $code;
    //         $new_user->save();
    //         // $token = "Bearer " . $user->createToken($request->email)->plainTextToken;
    //         $token = "Bearer " . $user->createToken('user-token', ['role:user'])->plainTextToken;
    //         dd( $new_user);

    //         $user_fromDataBase = User::find($new_user->id);
    //         $user_fromDataBase->token = $token;
    //         if ($new_user) {
    //             // return $this->data(compact('user'), 'User Created Successfully');
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'User Created Successfully You can check code now',
    //                 'data' => compact('user_fromDataBase'),

    //             ]);
    //         }
    //         else {
    //             // return $this->error_message('SomThing Went Wrong');
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'SomThing Went Wrong',
    //                 'data' => null,

    //             ]);
    //         }

    //     }//end if
    //     elseif ($user != null && $user->status = 0){
    //         $token = "Bearer " . $user->createToken('user-token', ['role:user'])->plainTextToken;
    //         $user->token = $token;
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'User Found  but not verfied',
    //             'data' => compact('user'),

    //         ]);
    //     }
    //     // dd($user_fromDataBase);

    // }
    // public function status(Request $request)
    // {
    //     $user = User::where('email', $request->email)->first();
    //     $user->status = 1;
    //     $user->save();
    //     return "Done";

    // }
}
