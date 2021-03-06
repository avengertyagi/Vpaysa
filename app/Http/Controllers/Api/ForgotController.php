<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ForgotController extends Controller
{
    public function forgotpassword(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $userData = $request->all();
            $rules = [
                'email'    => ['required'],
            ];
            $message = [
                'email.required'    => 'Please enter your email in format:yourname@gmail.com',
            ];
            //check validate data
            $validator = Validator::make($userData, $rules, $message);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $userDetails = User::where('email', $request['email'])->first();
            $token =  $userDetails->createToken('MyApp')->accessToken;
            if ($userDetails) {
                //check user active or not
                if ($userDetails['status'] == 0) {
                    return response()->json(['message' => 'your account has been block.Please contact to administrator']);
                }
                $userCount = User::where('email', $request['email'])->count();
                if ($userCount == 0) {
                    return response()->json(['message' => 'Email does not exit']);
                } else {
                    $random_password = Str::random(8);
                    $new_password = bcrypt($random_password);
                    $userUpdate = User::where('email', $request['email'])->update(['password' => $new_password]);
                    Helper::sendmail($userDetails, $userUpdate);
                    try {
                        $data = array();
                        $data['message'] = 'Mail has been send to your mail id.' . $request['email'];
                        $data['responsecode'] = '200';
                        $data['responsestatus'] = 'Ok';
                        //$data['new_password'] =  $random_password;
                        return response()->json($data);
                    } catch (Exception) {
                        return response()->json(['message' => 'ExpectationFailed'], 417);
                    }
                }
            } else {
                return response()->json(['message' => 'your email does not match.Please try again.']);
            }
        } else {
            return response()->json(['message' => 'Something went wrong. please try again!']);
        }
    }
}
