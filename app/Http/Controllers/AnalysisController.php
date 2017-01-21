<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stadium as Stadium;
use Auth;
use App\Customer as Customer;

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

    public function getStat(Request $request, $stadium)
    {
        
        $reservation = Stadium::where('id', Auth::user()->stadium_id)->first();

        $_mount = $request->input( '_date' );
        $first_day = date('01-m-Y', strtotime($_mount));
        $last_day = date('t-m-Y', strtotime($_mount));
        
        $chart = array();
        $outLabels = array();
        $i = 0;

        for($i = 0; $i < date('d', strtotime($last_day)); $i++)
        {
            $chart[$i]['labels'] = $i+1;
            $chart[$i]['data'] = 0;
            $outLabels[$i] = '' + $i+1;
        }

        foreach($reservation->field as $field)
        {
            foreach($field->reservation as $reserv)
            {
                if(strtotime($reserv['start_time']) >= strtotime($first_day) && strtotime($reserv['start_time']) <= strtotime($last_day))
                {
                    $chart[$i]['id'] = $reserv['customer_id'];
                    $chart[$i]['labels'] = intval(date('d', strtotime($reserv['start_time'])));
                    $chart[$i]['data'] = $reserv['field_price'];
                    $chart[$i]['ref_code'] = $reserv['ref_code'];
                    $i++;
                }
            }
        }

        $arrays = array();
        foreach($chart as $key => $item)
        {
            $arrays[$item['labels']][$key] = $item;
        }

        $income = 0;
        $outData = array();
        $outDayCount = array();
        $outBestCustomer = array();
        foreach ($arrays as $arr){
            foreach ($arr as $key => $value){
                foreach ($value as $key2 => $value2){
                    if($key2 == 'labels')
                    {
                        $index = $value['labels'];// .'-' .$key2.'-'.$value2;
                        if (array_key_exists($index, $outData)){
                            $outData[$index] += $value['data'];
                        } else {
                            $outData[$index] = 0;
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
                            $outBestCustomer[$index] = 1;
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
        $bestCustomerName = '';
        if($bestCutomer != null)
        {
            $max = array_keys($bestCutomer, max($bestCutomer));
            $customer = Customer::where('id', $max)->first();
            $bestCustomerName = $customer->nickname;
        }
        
        return array($outLabels, $outData, $income, $dayCount, $bestCustomerName);
    }
}
