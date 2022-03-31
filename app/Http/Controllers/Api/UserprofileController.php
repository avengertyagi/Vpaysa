<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Debitcard;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserprofileController extends Controller
{
    // User Card Save
    public function userCard(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $userData = $request->all();
            $rules = [
                'card_no' => ['required', 'numeric', 'min:16',  'unique:debit_card,card_no'],
                'expiry_month' => ['required', 'numeric', 'min:2'],
                'expiry_year' => ['required', 'numeric', 'min:2'],
                'cvc' => ['required', 'numeric', 'numeric', 'min:3', 'unique:debit_card,cvc'],
                'card_holder_name' => ['required', 'string'],
            ];
            $message = [
                'card_no.required' => 'Please enter your card number in format:42424242424242',
                'expiry_month.required' => 'Please enter your expiry month in format:02',
                'expiry_year.required'    => 'Please enter your expiry year in format:26',
                'cvc.required'    => 'Please enetr your phone in format:9999999999',
                'card_holder_name.required'  => 'Please enter your card hoder name in format:John Die',
            ];
            //check validate data
            $validator = Validator::make($userData, $rules, $message);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                //User get access token 
                $users =  User::where('remember_token', $request->header('token'))->first();
                //check user already added or not
                $dataCount = Debitcard::where('card_no', $request['card_no'])->count();
                if ($dataCount > 0) {
                    return response()->json('message', 'Card alredy exits.');
                } else {
                    $data = new Debitcard;
                    $data['user_id'] = $users['id'];
                    $data['expiry_month'] = $request['expiry_month'];
                    $data['expiry_year'] = $request['expiry_year'];
                    $data['card_no'] = $request['card_no'];
                    $data['cvc'] = $request['cvc'];
                    $data['card_holder_name'] = $request['card_holder_name'];
                    $data->created_at;
                    $data->save();
                    //return response user data
                    try {
                        $response_data = array();
                        $response_data['message'] = 'User Card added successfully';
                        $response_data['responsecode'] = '200';
                        $response_data['responsestatus'] = 'Ok';
                        $response_data['id'] = $data->id;
                        $response_data['user_id'] = $data->user_id;
                        $response_data['expiry_month'] = $data->expiry_month;
                        $response_data['expiry_year'] = $data->expiry_year;
                        $response_data['card_no'] = $data->card_no;
                        $response_data['cvc'] = $data->cvc;
                        $response_data['card_holder_name'] = $data->card_holder_name;
                        $response_data['created_at'] = $data->created_at;
                        return response()->json([$response_data], 200);
                    } catch (Exception) {
                        return response()->json(['message' => 'ExpectationFailed'], 417);
                    }
                }
            }
        } else {
            return response()->json('error', 'Something went wrong. please try again!');
        }
    }
    //user card update
    public function updateCard(Request $request, $id)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $userData = $request->all();
            $user = Debitcard::findOrFail($id);
            $rules = [
                'card_no' => ['required', 'numeric', 'min:16',  'unique:debit_card,card_no'],
                'expiry_month' => ['required', 'numeric', 'min:2'],
                'expiry_year' => ['required', 'numeric', 'min:2'],
                'cvc' => ['required', 'numeric', 'numeric', 'min:3', 'unique:debit_card,cvc'],
                'card_holder_name' => ['required', 'string'],
            ];
            $message = [
                'card_no.required' => 'Please enter your card number in format:42424242424242',
                'expiry_month.required' => 'Please enter your expiry month in format:02',
                'expiry_year.required'    => 'Please enter your expiry year in format:26',
                'cvc.required'    => 'Please enetr your phone in format:9999999999',
                'card_holder_name.required'  => 'Please enter your card hoder name in format:John Die',
            ];
            //check validate data
            $validator = Validator::make($userData, $rules, $message);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                //user card update
                $data = Debitcard::find($id);
                $data['expiry_month'] = $request['expiry_month'];
                $data['expiry_year'] = $request['expiry_year'];
                $data['card_no'] = $request['card_no'];
                $data['cvc'] = $request['cvc'];
                $data['card_holder_name'] = $request['card_holder_name'];
                $data->created_at;
                $data->update();
                //return response user data
                try {
                    $response_data = array();
                    $response_data['message'] = 'User Card update successfully';
                    $response_data['responsecode'] = '200';
                    $response_data['responsestatus'] = 'Ok';
                    $response_data['id'] = $data->id;
                    $response_data['user_id'] = $data->user_id;
                    $response_data['expiry_month'] = $data->expiry_month;
                    $response_data['expiry_year'] = $data->expiry_year;
                    $response_data['card_no'] = $data->card_no;
                    $response_data['cvc'] = $data->cvc;
                    $response_data['card_holder_name'] = $data->card_holder_name;
                    $response_data['created_at'] = $data->created_at;
                    return response()->json([$response_data], 200);
                } catch (Exception) {
                    return response()->json(['message' => 'ExpectationFailed'], 417);
                }
            }
        } else {
            return response()->json('error', 'Something went wrong. please try again!');
        }
    }
    // user save address
    public function saveAddress(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $userData = $request->all();
            $rules = [
                'street_one' => ['required', 'string', 'max:100'],
                'street_two' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:100'],
                'state' => ['required', 'string', 'max:100'],
                'country' => ['required', 'string'],
                'post_code' => ['required', 'numeric', 'min:6']
            ];
            $message = [
                'street_one.required' => 'Please enter your street 1 address',
                'street_two.required' => 'Please enter your street 2 address',
                'city.required'    => 'Please enter your city name',
                'state.required'    => 'Please enter your state name',
                'country.required'  => 'Please enter your country name',
                'post_code.required'  => 'Please enter your post code',
            ];
            //check validate data
            $validator = Validator::make($userData, $rules, $message);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                //User get access token 
                $users =  User::where('remember_token', $request->header('token'))->first();
                //dd($users);
                $data = new UserAddress;
                $data['user_id'] = $users['id'];
                $data['street_one'] = $request['street_one'];
                $data['street_two'] = $request['street_two'];
                $data['city'] = $request['city'];
                $data['state'] = $request['state'];
                $data['country'] = $request['country'];
                $data['post_code'] = $request['post_code'];
                $data->save();
                //return response user data
                try {
                    $response_data = array();
                    $response_data['message'] = 'User Address added successfully';
                    $response_data['responsecode'] = '200';
                    $response_data['responsestatus'] = 'Ok';
                    $response_data['id'] = $data->id;
                    $response_data['user_id'] = $data->user_id;
                    $response_data['street_one'] = $data->street_one;
                    $response_data['street_two'] = $data->street_two;
                    $response_data['city'] = $data->city;
                    $response_data['state'] = $data->state;
                    $response_data['country'] = $data->country;
                    $response_data['post_code'] = $data->post_code;
                    $response_data['created_at'] = $data->created_at;
                    return response()->json([$response_data], 200);
                } catch (Exception) {
                    return response()->json(['message' => 'ExpectationFailed'], 417);
                }
            }
        } else {
            return response()->json('error', 'Something went wrong. please try again!');
        }
    }
    public function updateAddress(Request $request, $id)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $userData = $request->all();
            $rules = [
                'street_one' => ['required', 'string', 'max:100'],
                'street_two' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:100'],
                'state' => ['required', 'string', 'max:100'],
                'country' => ['required', 'string'],
                'post_code' => ['required', 'numeric', 'min:6']
            ];
            $message = [
                'street_one.required' => 'Please enter your street 1 address',
                'street_two.required' => 'Please enter your street 2 address',
                'city.required'    => 'Please enter your city name',
                'state.required'    => 'Please enter your state name',
                'country.required'  => 'Please enter your country name',
                'post_code.required'  => 'Please enter your post code',
            ];
            //check validate data
            $validator = Validator::make($userData, $rules, $message);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                $useraddress = UserAddress::find($id);
                $useraddress['street_one'] = $request['street_one'];
                $useraddress['street_two'] = $request['street_two'];
                $useraddress['city'] = $request['city'];
                $useraddress['state'] = $request['state'];
                $useraddress['country'] = $request['country'];
                $useraddress['post_code'] = $request['post_code'];
                $useraddress->update();
                //return response user data
                try {
                    $response_data = array();
                    $response_data['message'] = 'User Address update successfully';
                    $response_data['responsecode'] = '200';
                    $response_data['responsestatus'] = 'Ok';
                    $response_data['id'] = $useraddress->id;
                    $response_data['user_id'] = $useraddress->user_id;
                    $response_data['street_one'] = $useraddress->street_one;
                    $response_data['street_two'] = $useraddress->street_two;
                    $response_data['city'] = $useraddress->city;
                    $response_data['state'] = $useraddress->state;
                    $response_data['country'] = $useraddress->country;
                    $response_data['post_code'] = $useraddress->post_code;
                    $response_data['created_at'] = $useraddress->created_at;
                    return response()->json([$response_data], 200);
                } catch (Exception) {
                    return response()->json(['message' => 'ExpectationFailed'], 417);
                }
            }
        } else {
            return response()->json('error', 'Something went wrong. please try again!');
        }
    }
    // user profile update
    public function updateProfile(Request $request, $id)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $userData = $request->all();
            $rules = [
                'firstname' => ['required', 'string', 'max:91'],
                'lastname' => ['required', 'string', 'max:91'],
                'username' => ['required', 'alpha_dash', 'min:5', 'unique:users,username'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
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
                $userProfile = User::find($id);
                if ($request->hasfile('image')) {
                    $destination = 'assets/uploads/user/' . $userProfile->image;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }
                    $file = $request->file('image');
                    $extension = $file->getClientoriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('assets/images/profile', $filename);
                    $userProfile->image = $filename;
                }
                $userProfile['firstname'] = $request['firstname'];
                $userProfile['lastname'] = $request['lastname'];
                $userProfile['username'] = $request['username'];
                $userProfile['email'] = $request['email'];
                $userProfile['phone'] = $request['phone'];
                $userProfile['balance'] = $request['balance'];
                $userProfile['address'] = $request['address'];
                $userProfile['created_at'];
                $userProfile->update();
                //return response user data
                try {
                    $data = array();
                    $data['message'] = 'User Profile update successfully';
                    $data['responsecode'] = '200';
                    $data['responsestatus'] = 'Ok';
                    $data['id'] = $userProfile->id;
                    $data['firstname'] = $userProfile->firstname;
                    $data['lastname'] = $userProfile->lastname;
                    $data['username'] = $userProfile->username;
                    $data['email'] = $userProfile->email;
                    $data['phone'] = $userProfile->phone;
                    $data['balance'] = $userProfile->balance;
                    $data['address'] = $userProfile->address;
                    $data['image']   = url('/') . '/public/assets/uploads/userProfile/' . $userProfile->image;
                    $data['created_at'] = $userProfile->created_at;
                    return response()->json([$data], 200);
                } catch (Exception) {
                    return response()->json(['message' => 'ExpectationFailed'], 417);
                }
            }
        } else {
            return response()->json('error', 'Something went wrong. please try again!');
        }
    }
}
