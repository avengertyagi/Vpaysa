<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function userLogout(Request $request)
    {
        $users =  User::where('remember_token', $request->header('token'))->first();
        //dd($users);
        if ($users) {
            if ($users['status'] == 0) {
                return response()->json(['message' => 'your account has been block.Please contact to administrator']);
            } else {
                $logoutUser = User::where('id', $users['id'])->update(['remember_token' => '']);
                $data = array();
                $data['message'] = 'User logout successfully';
                $data['responsecode'] = '402';
                $data['responsestatus'] = 'session expire';
                return response()->json($data);
            }
        } else {
            return response()->json(['message' => 'you are not login.Please be login again.']);
        }
    }
}
