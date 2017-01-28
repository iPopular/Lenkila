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
        $stadium_customer = Stadium::where('id', Auth::user()->stadium_id)->first();
        if($stadium != $stadium_customer->name)
            return Redirect::to('/');
        
        $events = array();
        $j = 0;
        foreach($stadium_customer->field as $field)
        {
            foreach($field->reservation as $reserv)
            {
                $events[$j]['id'] = $reserv['customer_id'];
                $events[$j]['start'] = date("H:i:s",strtotime($reserv['start_time']));
                $events[$j]['end'] = date("H:i:s",strtotime($reserv['end_time']));//$reserv['end_time'];
                $day = date('l', strtotime($reserv['start_time']));
                $events[$j]['day'] = $day;
                $events[$j]['ref_code'] = $reserv['ref_code'];
                $j++;
            }                       
            
        }
        $arrays = array();
        foreach($events as $key => $item)
        {
            $arrays[$item['id']][$key] = $item;
        }

        ksort($arrays, SORT_NUMERIC);
        //$count = $this->array_count($events, 'day');

        $outDay = array();
        $outTime = array();
        $outDayCount = array();
        foreach ($arrays as $arr){
            foreach ($arr as $key => $value){
                foreach ($value as $key2 => $value2){
                    if($key2 == 'day')
                    {
                        $index = $value['id'] .'-' .$key2.'-'.$value2;
                        if (array_key_exists($index, $outDay)){
                            $outDay[$index]++;
                        } else {
                            $outDay[$index] = 1;
                        }
                    }
                    if($key2 == 'ref_code')
                    {
                        $index = $value['id'] .'-' .$value['ref_code'];
                        if (array_key_exists($index, $outDay)){
                            $outDayCount[$index]++;
                        } else {
                            $outDayCount[$index] = 1;
                        }
                    }
                    if($key2 == 'start')
                    {
                        $index = $value['id'] .'-' .$key2.'-'.$value2;
                        if (array_key_exists($index, $outTime)){
                            $outTime[$index]++;
                        } else {
                            $outTime[$index] = 1;
                        }
                    }          
                }
            }
        }
        $tmp = array();
        foreach ($outDay as $key => $value) 
        {
            $key_str = explode("-", $key);           
            $tmp[$key_str[0]][$key_str[2]] = $value; 
        }
        $maxDay = array();
        foreach($tmp as $key => $value)
        {            
            $maxs = array_keys($value, max($value));
            $maxDay[$key] = $maxs;
        }
        
        $tmp2 = array();
        foreach ($outTime as $key => $value) 
        {
            $key_str = explode("-", $key);           
            $tmp2[$key_str[0]][$key_str[2]] = $value; 
        }
        $maxTime = array();
        foreach($tmp2 as $key => $value)
        {            
            $maxs = array_keys($value, max($value));
            $maxTime[$key] = $maxs;
        }

        $tmp3 = array();
        foreach ($outDayCount as $key => $value) 
        {
            $key_str = explode("-", $key);
            if($key_str[1] != 0)        
                $tmp3[$key_str[0]][$key_str[1]] = $value; 
        }
        $countDay = array();
        foreach($tmp3 as $key => $value)
        {            
            $countDay[$key] = array_sum($value);
        }
        
        return view('pages.customer_info', compact('stadium', 'stadium_customer', 'maxTime', 'maxDay', 'countDay'));
     
    }

    function array_count ($array, $key, $value = NULL) {
        // count($array[*][$key])
        $c = 0;
        if (is_null($value)) {
            foreach ($array as $i=>$subarray) {
                $c += ($subarray[$key]!='');
            }
        } else {
            foreach ($array as $i=>$subarray) {
                $c += ($subarray[$key]==$value);
            }
        }
        return $c;
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
                'workplace' => 'max:50',
                'birthday' => 'date_format:Y-m-d',
                'note'  => 'max:300'            
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
                if(Input::has('birthday'))                
                    $customer->birthday     = $request->input('birthday');
                $customer->workplace    = $request->input('workplace');
                $customer->created_by   = Auth::user()->id;
                $customer->save();
            }
        }
        $customer = Customer::where('mobile_number', $request->input('mobile_number'))->first();
        $member_id = str_pad(Auth::user()->stadium_id, 3, "0", STR_PAD_LEFT) . str_pad($customer->id, 5, "0", STR_PAD_LEFT);           
        $tmp_customer_stadium = Tmp_Customer_Stadium::where('member_id', $member_id)->first();
        
        if(count($tmp_customer_stadium) <= 0)
        {                
            $tmp_customer_stadium = new Tmp_Customer_Stadium();
            $tmp_customer_stadium->stadium_id   = Auth::user()->stadium_id;
            $tmp_customer_stadium->customer_id  = $customer->id;
            $tmp_customer_stadium->member_id    = $member_id;
            $tmp_customer_stadium->note         = $request->input('note');
            $tmp_customer_stadium->created_by   = Auth::user()->id;
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

    public function editCustomer(Request $request, $stadium)
    {
        $customer = Customer::where('mobile_number', $request->input('hdd_mobile_number'))->first();
        
        if(count($customer) > 0)
        {
            $member_id = str_pad(Auth::user()->stadium_id, 3, "0", STR_PAD_LEFT) . str_pad($customer->id, 5, "0", STR_PAD_LEFT); 
            $tmp_customer_stadium = Tmp_Customer_Stadium::where('member_id', $member_id)->first();

            if(count($tmp_customer_stadium) > 0)
            {
                $rules = array(
                    'firstname' => 'max:255',
                    'lastname' => 'max:255',
                    'nickname' => 'required',
                    'workplace' => 'max:50',
                    'birthday' => 'date_format:Y-m-d',
                    'note'  => 'max:300'
                );

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) 
                {                
                    Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลลูกค้าได้!');
                    return Redirect::to('/'. $stadium .'/customer_info')
                        ->withErrors($validator)
                        ->withInput(Input::except('password'));
                }
                else
                {
                    $customer->mobile_number  	= $request->input('mobile_number');
                    $customer->firstname	= $request->input('firstname');
                    $customer->lastname   	= $request->input('lastname');
                    $customer->nickname  	= $request->input('nickname');                    
                    $customer->sex          = $request->input('sex');
                    if(Input::has('birthday'))
                        $customer->birthday     = $request->input('birthday');
                    $customer->workplace    = $request->input('workplace');
                    $customer->updated_by   = Auth::user()->id;
                    $customer->save();

                    $tmp_customer_stadium->note = $request->input('note');
                    $tmp_customer_stadium->updated_by = Auth::user()->id;
                    $tmp_customer_stadium->save();

                    Session::flash('success_msg', 'แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว!');
                    return Redirect::to('/'. $stadium .'/customer_info');
                }
            }            
        }
        
        Session::flash('error_msg', 'ไม่พบข้อมูลลูกค้าในฐานข้อมูล! กรุณาสร้างข้อมูลลูกค้า');
        return Redirect::to('/'. $stadium .'/customer_info')
            ->withInput(Input::except('password'));
        
    }

    public function deleteCustomer(Request $request , $stadium)
    {
        $customer = Customer::where('mobile_number', $request->input('del-customer'))->first();
        if(count($customer) > 0)
        {
            $member_id = str_pad(Auth::user()->stadium_id, 3, "0", STR_PAD_LEFT) . str_pad($customer->id, 5, "0", STR_PAD_LEFT); 
            $tmp_customer_stadium = Tmp_Customer_Stadium::where('member_id', $member_id)->first();

            if(count($tmp_customer_stadium) > 0)
            {
                $tmp_customer_stadium->delete();
                Session::flash('success_msg', 'ลบข้อมูลลูกค้าเรียบร้อยแล้ว!');
                    return Redirect::to('/'. $stadium .'/customer_info');
            }
        }

        Session::flash('error_msg', 'ไม่พบข้อมูลลูกค้าในฐานข้อมูล! กรุณาสร้างข้อมูลลูกค้า');
        return Redirect::to('/'. $stadium .'/customer_info')
            ->withInput(Input::except('password'));
    }
}
