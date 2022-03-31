<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\TokenRepository;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\RefreshTokenRepository;

class ChangepasswordController extends Controller
{
    public function changePassword(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $userData = $request->all();
        $rules = [
            'oldpassword' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
        $message = [
            'oldpassword' => ['Please enter your oldpassword'],
            'password'   => ['Please enter your new password'],
        ];
        //check validate data
        $validator = Validator::make($userData, $rules, $message);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //check user registered or not
        $user = User::where('remember_token', $request->header('token'))->first();
        //dd($user);
        if ($user) {
            //check user active or not
            if ($user['status'] == 0) {
                return response()->json(['message' => 'your account has been block.Please contact to administrator']);
            }
            if (!$token = Auth::attempt(['id' => $user['id'], 'password' =>  $request['oldpassword']])) {
                return response()->json(['message' => 'your old password does not match.']);
            } else {
                $updateUser = User::where('id', $user['id'])->update(['password' => Hash::make($request['password'])]);
                try {
                    $data = array();
                    $data['message'] = 'User password changed successfully.';
                    $data['responsecode'] = '200';
                    $data['responsestatus'] = 'Ok';
                    return response()->json($data);
                } catch (Exception) {
                    return response()->json(['message' => 'ExpectationFailed'], 417);
                }
            }
        } else {
            return response()->json(['message' => 'your old password does not match.']);
        }
    }
}
