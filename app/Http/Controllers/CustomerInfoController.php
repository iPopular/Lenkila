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

    public function addCustomer(Request $request)
    {
        $rules = array(
            'firstname' => 'max:255',
            'lastname' => 'max:255',
            'nickname' => 'required|unique:customer',
            'mobile-number' => 'required|unique:customer',
            'workplace' => 'required|max:50',
        );

        $validator = Validator::make($request::all(), $rules);

        if ($validator->fails()) 
        {
            Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลลูกค้าได้!');
        	return Redirect::to('/account_management')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
        else
        {
            $customer = New Customer();
            $customer->firstname	= $request->input('firstname');
	        $customer->lastname   	= $request->input('lastname');
	        $customer->nickname  	= $request->input('nickname');
	        $customer->mobile_number = $request->input('mobile-number');
            $customer->sex          = $request->input('male');
            $customer->birthday     = $request->input('birthday');
            $customer->workplace    = $request->input('workplace');
            $customer->created_by   = Auth::user()->id;
            $customer->save();
            Session::flash('success_msg', 'เพิ่มข้อมูลลูกค้าเรียบร้อยแล้ว!');
	        return Redirect::to('/account_management');
        }
    }
}
