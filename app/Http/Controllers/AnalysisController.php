<?php

namespace App\Http\Controllers;

use Validator;
use Session;
use Hash;
use Auth;
use Illuminate\Http\Request;
use App\Stadium as Stadium;
use App\Tmp_Customer_Stadium as Tmp_Customer_Stadium;
use App\Customer as Customer;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use DatePeriod;

class AnalysisController extends Controller
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
        return view('pages.analysis', compact('stadium'));        
    }

    public function editBestCustomer(Request $request, $stadium)
    {
        $customer = Customer::where('mobile_number', $request->input('hdd_mobile_number'))->first();
        
        if(count($customer) > 0)
        {
            $member_id = str_pad(Auth::user()->stadium_id, 3, "0", STR_PAD_LEFT) . str_pad($customer->id, 5, "0", STR_PAD_LEFT); 
            $tmp_customer_stadium = Tmp_Customer_Stadium::where('member_id', $member_id)->first();

            if(count($tmp_customer_stadium) > 0)
            {
                $rules = array(
                    'note'  => 'max:300'
                );

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) 
                {                
                    Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลลูกค้าได้!');
                    return Redirect::to('/'. $stadium .'/analysis')
                        ->withErrors($validator)
                        ->withInput(Input::except('password'));
                }
                else
                {                   
                    $tmp_customer_stadium->note = $request->input('note');
                    $tmp_customer_stadium->updated_by = Auth::user()->id;
                    $tmp_customer_stadium->save();

                    Session::flash('success_msg', 'แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว!');
                    return Redirect::to('/'. $stadium .'/analysis');
                }
            }            
        }
        
        Session::flash('error_msg', 'ไม่พบข้อมูลลูกค้าในฐานข้อมูล! กรุณาสร้างข้อมูลลูกค้า');
        return Redirect::to('/'. $stadium .'/analysis')
            ->withInput(Input::except('password'));
    }

    public function getStat(Request $request, $stadium)
    {
        
        $reservation = Stadium::where('id', Auth::user()->stadium_id)->first();

        $_mount = $request->input( '_date' );
        $first_day = date('01-m-Y', strtotime($_mount));
        $last_day = date('t-m-Y', strtotime($_mount));
        $i = 0;
        $chart = array();
        $outDateLabels = array();
        
        
        for($i = 0; $i < date('d', strtotime($last_day)); $i++)
        {
            $chart[$i]['labels'] = $i;
            $chart[$i]['data'] = 0;
            $outDateLabels[$i] = $i + 1;
        }        

        foreach($reservation->field as $field)
        {
            foreach($field->reservation as $reserv)
            {
                if(strtotime($reserv['start_time']) >= strtotime($first_day) && strtotime($reserv['start_time']) <= strtotime($last_day))
                {
                    $chart[$i]['id'] = $reserv['customer_id'];
                    $chart[$i]['labels'] = intval(date('d', strtotime($reserv['start_time']))) - 1;
                    $chart[$i]['data'] = $reserv['field_price'];
                    $chart[$i]['water_price'] = $reserv['water_price'];
                    $chart[$i]['supplement_price'] = $reserv['supplement_price'];
                    $chart[$i]['ref_code'] = $reserv['ref_code'];
                    $chart[$i]['start'] = date("H:i:s",strtotime($reserv['start_time']));
                    $i++;
                }
            }
        }

        $arrays = array();
        foreach($chart as $key => $item)
        {
            $arrays[$item['labels']][$key] = $item;
        }

        $chartByDay = $this->getLineChartByDay($reservation, $first_day, $last_day);
        $chartByTime = $this->getLineChartByTime($reservation, $first_day, $last_day);
        

        $income = 0;
        $outDataAmount = array();
        $outDataCnt = array();
        $tmpDataCnt = array();
        $outDayCount = array();
        $outBestCustomer = array();
        
        $outTime = array();
        foreach ($arrays as $arr){
            foreach ($arr as $key => $value){
                foreach ($value as $key2 => $value2){
                    if($key2 == 'labels')
                    {
                        $index = $value['labels'];// .'-' .$key2.'-'.$value2;
                        if (array_key_exists($index, $outDataAmount)){
                            $outDataAmount[$index] += $value['data'];
                        } else {
                            $outDataAmount[$index] = 0;
                        }
                        if(array_key_exists('id', $value))
                        {
                            $index = $value['labels'] .'-' . $value['id'];
                            if (array_key_exists($index, $tmpDataCnt)){
                                $tmpDataCnt[$index]++;
                                //$outDataDay[$value['labels']]++;
                            } else {
                                $tmpDataCnt[$index] = 1;
                                $outDataCnt[$value['labels']]++;
                            }
                        }
                        else
                        {
                            $outDataCnt[$value['labels']] = 0;
                        }
                        
                        
                    }
                    if($key2 == 'data')
                    {
                        $income+=$value['data'];
                    }
                    if($key2 == 'ref_code')
                    {
                        $index = $value['ref_code'];
                        if (!array_key_exists($index, $outDayCount)){
                            $outDayCount[$index] = 1;
                        }
                        $index = $value['id'] .'-' . $value['ref_code'];
                        if (!array_key_exists($index, $outBestCustomer)){
                            $outBestCustomer[$index] = $value['data'] + $value['water_price'] + $value['supplement_price'];
                        }
                        else
                        {
                            $outBestCustomer[$index] += $value['data'] + $value['water_price'] + $value['supplement_price'];
                        }
                        
                    }
                    if($key2 == 'start')
                    {
                        $index = $value['id'] . '-' . $value2;
                        if (array_key_exists($index, $outTime)){
                            $outTime[$index]++;
                        } else {
                            $outTime[$index] = 1;
                        }
                    }    
                }
            }
        }
        $dayCount = array_sum($outDayCount);

        $tmp = array();
        foreach ($outBestCustomer as $key => $value) 
        {
            $key_str = explode("-", $key);           
            $tmp[$key_str[0]][$key_str[1]] = $value; 
        }
        $bestCutomer = array();
        foreach($tmp as $key => $value)
        {            
            $bestCutomer[$key] = array_sum($value);
        }

        $tmp2 = array();
        foreach ($outTime as $key => $value) 
        {
            $key_str = explode("-", $key);           
            $tmp2[$key_str[0]][$key_str[1]] = $value; 
        }
        $maxTime = array();
        foreach($tmp2 as $key => $value)
        {            
            $maxs = array_keys($value, max($value));
            $maxTime[$key] = $maxs;
        }

        $bestCustomerName = '';
        $totalVisited = '';
        $customer = array();
        $reserves = array();
        $customer_note = '';
        if($bestCutomer != null)
        {
            $max_key = array_keys($bestCutomer, max($bestCutomer));
            $totalVisited = max($bestCutomer);
            $customer = Customer::where('id', $max_key)->first();
            $bestCustomerName = $customer->nickname;
            $member_id = str_pad(Auth::user()->stadium_id, 3, "0", STR_PAD_LEFT) . str_pad($customer->id, 5, "0", STR_PAD_LEFT); 
            $tmp_customer_stadium = Tmp_Customer_Stadium::where('member_id', $member_id)->first();
            $customer_note = $tmp_customer_stadium->note;
            $reserves = DB::table('reservation')->selectRaw('count(*)')->where('customer_id', '=', $customer->id)->groupBy('ref_code')->get();
            
            
        }
        
        return array($outDateLabels, $outDataAmount, $outDataCnt, $income, $dayCount, array($bestCustomerName, $customer, count($reserves), $maxTime, $customer_note), $chartByDay, $chartByTime);
    }

    public function getLineChartByDay($reservation, $first_day, $last_day)
    {
        $outDayLabels = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        $outDataAmount = array();
        $outDataCnt = array();
        $tmpDataCnt = array();

        $i = 0;
        $chart = array();
       
        for($i = 0; $i < 7; $i++)
        {
            $chart[$i]['days'] = $i;
            $chart[$i]['data'] = 0;
        }   

        foreach($reservation->field as $field)
        {
            foreach($field->reservation as $reserv)
            {
                if(strtotime($reserv['start_time']) >= strtotime($first_day) && strtotime($reserv['start_time']) <= strtotime($last_day))
                {
                    $chart[$i]['id'] = $reserv['customer_id'];
                    $chart[$i]['days'] = intval(date('w', strtotime($reserv['start_time'])));
                    $chart[$i]['data'] = $reserv['field_price'];
                    $i++;
                }
            }
        }

        $allData = array();
        foreach($chart as $key => $item)
        {
            $allData[$item['days']][$key] = $item;
        }

        foreach ($allData as $arr){
            foreach ($arr as $key => $value){
                foreach ($value as $key2 => $value2){

                    if($key2 == 'days')
                    {
                        $index = $value['days'];
                        if (array_key_exists($index, $outDataAmount)){
                            $outDataAmount[$index] += $value['data'];
                        } else {
                            $outDataAmount[$index] = 0;
                        }
                        if(array_key_exists('id', $value))
                        {
                            $index = $value['days'] .'-' . $value['id'];
                            if (array_key_exists($index, $tmpDataCnt)){
                                $tmpDataCnt[$index]++;
                            } else {
                                $tmpDataCnt[$index] = 1;
                                $outDataCnt[$value['days']]++;
                            }
                        }
                        else
                        {
                            $outDataCnt[$value['days']] = 0;
                        }
                    }
                }
            }
        }

        return array($outDataAmount, $outDataCnt, $outDayLabels);
    }

    public function getLineChartByTime($reservation, $first_day, $last_day)
    {
        $openTime = intval(date('G', strtotime($reservation->open_time)));
        $closeTime = intval(date('G', strtotime($reservation->close_time)));
        $outTimeLabels = array();
        $outDataAmount = array();
        $outDataCnt = array();
        $tmpDataCnt = array();

        $i = 0;
        $chart = array();
        
        $begin = new Datetime($reservation->open_time);
        $end = new Datetime($reservation->close_time);
        if($end <= $begin)
            $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 hour');
        $period = new DatePeriod($begin, $interval, $end);

        foreach ( $period as $dt )
        {            
            $chart[$i]['times'] = intval(date_format($dt, 'G')) - 7;
            $chart[$i]['data'] = 0;
            $outTimeLabels[$i] = date_format($dt, 'g:i A');
            $i++;
        }

        foreach($reservation->field as $field)
        {
            foreach($field->reservation as $reserv)
            {
                if(strtotime($reserv['start_time']) >= strtotime($first_day) && strtotime($reserv['start_time']) <= strtotime($last_day))
                {
                    $chart[$i]['ref_code'] = $reserv['ref_code'];
                    $chart[$i]['times'] = intval(date('G', strtotime($reserv['start_time']))) - 7;
                    $chart[$i]['data'] = $reserv['field_price'];
                    $i++;
                }
            }
        }

        $allData = array();
        foreach($chart as $key => $item)
        {
            $allData[$item['times']][$key] = $item;
        }
        

        foreach ($allData as $arr){
            foreach ($arr as $key => $value){
                foreach ($value as $key2 => $value2){

                    if($key2 == 'times')
                    {
                        $index = $value['times'];
                        if (array_key_exists($index, $outDataAmount)){
                            $outDataAmount[$index] += $value['data'];
                        } else {
                            $outDataAmount[$index] = 0;
                        }
                        if(array_key_exists('ref_code', $value))
                        {
                            $index = $value['times'] .'-' . $value['ref_code'];
                            if (array_key_exists($index, $tmpDataCnt)){
                                $tmpDataCnt[$index]++;
                            } else {
                                $tmpDataCnt[$index] = 1;                                
                                $outDataCnt[$value['times']]++;                                
                            }
                        }
                        else
                        {
                            $outDataCnt[$value['times']] = 0;
                        }
                    }
                }
            }
        }

        return array($outDataAmount, $outDataCnt, $outTimeLabels, $allData);
    }
}
