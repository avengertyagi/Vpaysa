<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
    public function storeRegister(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $userData = $request->all();
            $rules = [
                'firstname' => ['required', 'string', 'max:91'],
                'lastname' => ['required', 'string', 'max:91'],
                'username' => ['required', 'alpha_dash', 'min:5', 'unique:users,username'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string'],
                'phone'    => ['required', 'numeric', 'min:10'],
                'balance'  => ['required', 'numeric'],
                'address'  => ['required', 'string', 'max:100'],
                'image'    => ['required', 'mimes:jpeg,jpg,png', 'max:1000'],
            ];
            $message = [
                'firstname.required' => 'Please enter your firstname in format:John',
                'lastname.required' => 'Please enter your lastname in format:Die',
                'email.required'    => 'Please enter your email in format:yourname@gmail.com',
                'phone.required'    => 'Please enetr your phone in format:9999999999',
                'balance.required'  => 'Please enter your balance',
                'image.required'    => 'Please enter your image',
                'address.required'  => 'Please enter your address'
            ];
            //check validate data
            $validator = Validator::make($userData, $rules, $message);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                //check user alredy exits or not
                $user = User::where('email', $request['email'])->orWhere('phone', $request['phone'])->count();
                if ($user > 0) {
                    return response()->json(['message' => 'Email and mobile number already exits']);
                } else {
                    // save user data
                    $user = new User;
                    if ($request->hasfile('image')) {
                        $file = $request->file('image');
                        $extension = $file->getClientoriginalExtension();
                        $filename = time() . '.' . $extension;
                        $file->move('assets/uploads/user/', $filename);
                        $user['image'] = $filename;
                    }
                    $user['firstname'] = $request['firstname'];
                    $user['lastname'] = $request['lastname'];
                    $user['username'] = $request['username'];
                    $user['email'] = $request['email'];
                    $user['password'] = Hash::make($request['password']);
                    $user['phone'] = $request['phone'];
                    $user['balance'] = $request['balance'];
                    $user['address'] = $request['address'];
                    $user['device_type'] = Helper::get_device();
                    $user['device_os'] = Helper::get_os();
                    $user['device_token'] = Str::random(100);
                    $user['created_at'];
                    $user->save();
                    //return response user data
                    try {
                        $data = array();
                        $data['message'] = 'User register added successfully';
                        $data['responsecode'] = '200';
                        $data['responsestatus'] = 'Ok';
                        $data['id'] = $user->id;
                        $data['firstname'] = $user->firstname;
                        $data['lastname'] = $user->lastname;
                        $data['username'] = $user->username;
                        $data['email'] = $user->email;
                        $data['phone'] = $user->phone;
                        $data['balance'] = $user->balance;
                        $data['address'] = $user->address;
                        $data['image']   = url('/') . '/public/assets/uploads/user/' . $user->image;
                        $data['device_type'] = $user->device_type;
                        $data['device_os'] =  $user->device_os;
                        $data['device_token'] = $user->device_token;
                        $data['created_at'] = $user->created_at;
                        return response()->json([$data], 200);
                    } catch (Exception $e) {
                        return response()->json(['message' => 'ExpectationFailed'], 417);
                    }
                }
            }
        } else {
            return response()->json('error', 'Something went wrong. please try again!');
        }
    }
}
