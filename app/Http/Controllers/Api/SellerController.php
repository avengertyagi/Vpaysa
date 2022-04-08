<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SellerTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SellerController extends Controller
{
    public function storeSeller(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $userData = $request->all();
            $amount = $request->amount;
            $auth = $request->id;
            $rules = [
                'title' => ['required', 'string', 'max:91'],
                'is_project' => ['required', 'string', 'max:91'],
                'short_description' => ['required', 'string', 'max:255'],
                'specification'    => ['required', 'string', 'max:255'],
                'deal_condition' => ['required', 'string', 'max:100'],
                'is_broker'    => ['required'],
                'amount'  => ['required', 'numeric', 'min:1500'],
                'image'    => ['required', 'mimes:jpeg,jpg,png', 'max:1000'],
                'brokrage_fee'  => ['required', 'string', 'max:100'],
                'payment_type' => ['required'],
                'who_pay'  => ['required'],
            ];
            $message = [
                'title.required' => 'Please enter your title',
                'is_project.required' => 'Please enter your project',
                'short_description.required'    => 'Please enter your short description',
                'specification.required'    => 'Please enetr your specification',
                'deal_condition.required'  => 'Please enter deal condition',
                'image.required'    => 'Please enter your image',
                'amount.required'  => 'Please enter your address'
            ];
            //check validate data
            $validator = Validator::make($userData, $rules, $message);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                //save seller transaction
                if ($request->brokrage_fee != '') {
                    $brokerCharge = ($amount * $request->brokrage_fee) / 100;
                }
                $data = DB::table('prices')->get();
                $user = User::select('balance')->where('id', $auth)->first();
                $fee = 0;
                foreach ($data as $key => $item) {
                    if ($amount >= $item->from && $amount <= $item->including) {
                        $fee = $item->trans_fee;
                    }
                }
                $fee = preg_replace('/[%$?]/s', '', $fee);
                $charge = ($amount * $fee) / 100;
                if ($amount < $request['amount']) {
                    return response()->json(['message' => 'minimum amount', 402]);
                }
                if ($amount > $request['amount']) {
                    return response()->json(['message' => 'maximum amount']);
                }
                if (($user->balance  + $brokerCharge) < $amount) {
                    return response()->json(['message' => 'Insufficient Balance.']);
                }
                $dealcode =  Str::random(20);
                $dealLink =  url('/') . '{$dealcode}';
                $transaction = new SellerTransaction();
                if ($request->hasfile('image')) {
                    $file = $request->file('image');
                    $extension = $file->getClientoriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('assets/uploads/seller/', $filename);
                    $transaction->file = $filename;
                }
                $transaction['creator_id']       = $request['id'];
                $transaction['joiner_id']        = $user['id'];
                $transaction->title              = $request['title'];
                $transaction->is_project         = strtolower($request['is_project']);
                $transaction->short_description  = $request['short_description'];
                $transaction->specification      = $request['specification'];
                $transaction->deal_condition     = $request['deal_condition'];
                $transaction->amount             = $request['amount'];
                $transaction->is_broker          = $request['is_broker'];
                $transaction->brokrage_fee       = $request['brokrage_fee'];
                $transaction->payment_type       = strtolower($request['payment_type']);
                $transaction->who_pay            = strtolower($request['who_pay']);
                $transaction->deal_link          = $dealcode;
                $transaction->deal_code          = $dealLink;
                $transaction->charge             = $request['charge'];
                $transaction->broker_charge      = $request['broker_charge'];
                $transaction->invoice            = mt_rand(10000000, 99999999);
                //dd($transaction);
                $transaction->save();
                //return response user data
                try {
                    $response_data = array();
                    $response_data['message'] = 'Your request has been sent';
                    $response_data['responsecode'] = '200';
                    $response_data['responsestatus'] = 'Ok';
                    $response_data['id'] = $transaction->id;
                    $response_data['user_id'] = $transaction->$user;
                    $response_data['title'] = $transaction->title;
                    $response_data['is_project'] = $transaction->is_project;
                    $response_data['short_description'] = $transaction->short_description;
                    $response_data['specification'] = $transaction->specification;
                    $response_data['deal_condition'] = $transaction->deal_condition;
                    $response_data['amount'] = $transaction->amount;
                    $response_data['is_broker'] = $transaction->is_broker;
                    $response_data['brokrage_fee'] = $transaction->brokrage_fee;
                    $response_data['payment_type'] = $transaction->payment_type;
                    $response_data['who_pay'] = $transaction->who_pay;
                    $response_data['deal_link'] = $transaction->deal_link;
                    $response_data['deal_code'] = $transaction->deal_code;
                    $response_data['charge'] = $transaction->charge;
                    $response_data['broker_charge'] = $transaction->broker_charge;
                    $response_data['invoice'] = $transaction->invoice;
                    $response_data['created_at'] = $transaction->created_at;
                    return response()->json([$response_data], 200);
                } catch (Exception) {
                    return response()->json(['message' => 'ExpectationFailed'], 417);
                }
            }
        } else {
            return response()->json(['message' => 'Something went wrong. please try again!']);
        }
    }
    public function myTransaction(Request $request)
    {
        $user = User::where('remember_token', $request->header('token'))->first();
        $transaction_list = SellerTransaction::where('creator_id', $user->id)
            ->orWhere('joiner_id', $user->id)->with(['user', 'invitee'])->get();
        //dd($transaction_list);
        return response()->json([$transaction_list], 200);
        // try {
        //     $response_data = array();
        //     $response_data['message'] = 'Fetch list successfuly';
        //     $response_data['responsecode'] = '200';
        //     $response_data['responsestatus'] = 'Ok';
        //     $response_data['id'] = $transaction_list->id;
        //     //$response_data['user_id'] = $transaction_list->$user;
        //     $response_data['title'] = $transaction_list->title;
        //     $response_data['is_project'] = $transaction_list->is_project;
        //     $response_data['short_description'] = $transaction_list->short_description;
        //     $response_data['specification'] = $transaction_list->specification;
        //     $response_data['deal_condition'] = $transaction_list->deal_condition;
        //     $response_data['amount'] = $transaction_list->amount;
        //     $response_data['is_broker'] = $transaction_list->is_broker;
        //     $response_data['brokrage_fee'] = $transaction_list->brokrage_fee;
        //     $response_data['payment_type'] = $transaction_list->payment_type;
        //     $response_data['who_pay'] = $transaction_list->who_pay;
        //     $response_data['deal_link'] = $transaction_list->deal_link;
        //     $response_data['deal_code'] = $transaction_list->deal_code;
        //     $response_data['charge'] = $transaction_list->charge;
        //     $response_data['broker_charge'] = $transaction_list->broker_charge;
        //     $response_data['invoice'] = $transaction_list->invoice;
        //     $response_data['created_at'] = $transaction_list->created_at;
        //     dd($response_data);
        //     return response()->json([$response_data], 200);
        // } catch (Exception) {
        //     return response()->json(['message' => 'ExpectationFailed'], 417);
        // }
    }
}
