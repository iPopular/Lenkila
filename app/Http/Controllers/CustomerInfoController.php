<?php

namespace App\Http\Controllers;

use Validator;
use Session;
use Hash;
use Auth;
use Illuminate\Http\Request;
use App\Customer as Customer;
use App\Stadium as Stadium;
use App\Role as Role;
use App\Tmp_Customer_Stadium as Tmp_Customer_Stadium;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class CustomerInfoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request, $stadium)
    {
        $stadium_customer = Stadium::find(Auth::user()->stadium_id)->first();
        if($stadium != $stadium_customer->name)
            return Redirect::to('/');
        
        return view('pages.customer_info', compact('stadium', 'stadium_customer'));
     
    }

    public function addCustomer(Request $request, $stadium)
    {
        $customer = Customer::where('mobile_number', $request->input('mobile_number'))->first();        

        if(count($customer) <= 0)
        {
            $rules = array(
                'firstname' => 'max:255',
                'lastname' => 'max:255',
                'nickname' => 'required',
                'mobile_number' => 'required|unique:customer',
                'workplace' => 'required|max:50',
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
            {                
                Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลลูกค้าได้!');
                return Redirect::to('/'. $stadium .'/customer_info')
                    ->withErrors($validator)
                    ->withInput(Input::except('password'));
            }
            else
            {
                $customer = New Customer();
                $customer->firstname	= $request->input('firstname');
                $customer->lastname   	= $request->input('lastname');
                $customer->nickname  	= $request->input('nickname');
                $customer->mobile_number = $request->input('mobile_number');
                $customer->sex          = $request->input('sex');
                $customer->birthday     = $request->input('birthday');
                $customer->workplace    = $request->input('workplace');
                $customer->created_by   = Auth::user()->id;
                $customer->save();
            }
        }
        
        $member_id = str_pad(Auth::user()->stadium_id, 3, "0", STR_PAD_LEFT) . str_pad($customer->id, 5, "0", STR_PAD_LEFT);           
        $tmp_customer_stadium = Tmp_Customer_Stadium::where('member_id', $member_id)->first();
        
        if(count($tmp_customer_stadium) <= 0)
        {                
            $tmp_customer_stadium = new Tmp_Customer_Stadium();
            $tmp_customer_stadium->stadium_id   = Auth::user()->stadium_id;
            $tmp_customer_stadium->customer_id  = $customer->id;
            $tmp_customer_stadium->member_id    = $member_id;
            $tmp_customer_stadium->save();
            Session::flash('success_msg', 'เพิ่มข้อมูลลูกค้าเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium .'/customer_info');
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลลูกค้าได้ มีรหัสซ้ำ!');
                return Redirect::to('/'. $stadium .'/customer_info');
        }
        
    }
}
