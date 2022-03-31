<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function userLogin(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $userData = $request->all();
        $rules = [
            'email'    => ['required'],
            'password' => ['required'],
        ];
        $message = [
            'email.required'    => 'Please enter your email in format:yourname@gmail.com',
            'phone.required'    => 'Please enetr your phone in format:9999999999',
        ];
        //check validate data
        $validator = Validator::make($userData, $rules, $message);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            //check user registered or not
            $user = User::where('email', $request['email'])->first();
            $token =  $user->createToken('MyApp')->accessToken;
            if ($user) {
                //check user active or not
                if ($user['status'] == 0) {
                    return response()->json(['message' => 'your account has been block.Please contact to administrator']);
                }
                if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
                    $update = User::where('id', $user['id'])->update(['remember_token' => $token]);
                    //return response user data
                    $data = array();
                    $data['message'] = 'User login successfully';
                    $data['responsecode'] = '200';
                    $data['responsestatus'] = 'Ok';
                    $data['id'] = $user->id;
                    $data['firstname'] = $user->firstname;
                    $data['lastname'] = $user->lastname;
                    $data['username'] = $user->username;
                    $data['email'] = $user->email;
                    $data['password'] = $user->password;
                    $data['phone'] = $user->phone;
                    $data['balance'] = $user->balance;
                    $data['address'] = $user->address;
                    $data['image']   = url('/') . '/public/assets/uploads/user/' . $user->image;
                    $data['token'] = $token;
                    return response()->json([$data]);
                } else {
                    return response()->json(['message' => 'your email or password does not match.Please be login again.']);
                }
            } else {
                return response()->json(['message' => 'Something went wrong. please try again!']);
            }
        }
    }
}
