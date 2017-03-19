<?php

namespace App\Http\Controllers;

use Auth;
use App\Customer as Customer;
use App\Field as Field;
use App\User as User;
use App\Reservation as Reservation;
use App\Promotions as Promotions;
use App\Tmp_Field_Price as Tmp_Field_Price;
use App\Tmp_Customer_Stadium as Tmp_Customer_Stadium;
use App\Stadium as Stadium;
use App\Holidays as Holidays;
use Validator;
use Session;
use DateTime;
use DateInterval;
use DatePeriod;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;


class ReservationController extends Controller
{

    public function show(Request $request, $stadium)
    {
        //$fields = Field::where('stadium_id', Auth::user()->stadium_id)->where('status', '1')->get();
        $resource = array();
        $i = 0;
        
        $reservation = Stadium::where('id', Auth::user()->stadium_id)->first();
        $events = array();
        $holidays = array();
        $holidays2 = array();
        $j = 0;
        $k = 0;

        $openTime = intval(date('G', strtotime($reservation->open_time)));
        $closeTime = intval(date('G', strtotime($reservation->close_time)));
        $dateTimeOpenTime = new DateTime($reservation->open_time);
        $dateTimeCloseTime = new DateTime($reservation->close_time);


        $openTime = $openTime . ':00:00';
        $closeTime = $closeTime . ':00:00';

        foreach($reservation->holidays as $holiday)
        {
            if(new Datetime($holiday->start_time) >= new Datetime($holiday->end_time))   
                array_push($holidays, array('id' => $holiday->id, 'start' => date('Y-m-d H:i:s', strtotime("$holiday->start_date $holiday->start_time")), 'end' => date('Y-m-d H:i:s', strtotime("$holiday->end_date $holiday->end_time"  . "+1 day"))));
            else
                array_push($holidays, array('id' => $holiday->id, 'start' => date('Y-m-d H:i:s', strtotime("$holiday->start_date $holiday->start_time")), 'end' => date('Y-m-d H:i:s', strtotime("$holiday->end_date $holiday->end_time"))));
            
        }
        $holidays2[0]['start'] = date('Y-m-d', strtotime($holidays[count($holidays) - 1]['end'] . "-1 year"));
        $holidays2[0]['end'] = date('Y-m-d', strtotime($holidays[0]['start']));
        for($k = 1; $k <= count($holidays); $k++)
        {

            $holidays2[$k]['start'] = $holidays[$k - 1]['end'];
            if($k < count($holidays))
                $holidays2[$k]['end'] = $holidays[$k]['start'];
            else
                $holidays2[$k]['end'] = date('Y-m-d', strtotime($holidays[0]['start'] . "+1 year"));
        } 

        Log::info('holiday: '. json_encode($holidays));
        Log::info('holiday2: '. json_encode($holidays2));

        foreach($reservation->field as $field)
        {
            
            $resource[$i]['id'] = $field['id'];
            $resource[$i]['title'] = $field['name'];
            $resource[$i]['detail'] = $field['detail'];
            $resource[$i]['status'] = $field['status'];
            
            if($field['status'] == 0)
            {
                $events[$j]['resourceId'] = $field['id'];
                $events[$j]['start'] = '00:00:00';
                $events[$j]['end'] = '24:00:00';
                $events[$j]['rendering'] = 'background';
                $events[$j]['color'] = '#c1c1c1';
                $events[$j]['overlap'] = false;
                $events[$j]['ranges'] = array(array('start' => '2010-01-01', 'end' => '9999-01-01'));
                $j++;
            }
            else if($dateTimeCloseTime < $dateTimeOpenTime)
            {                

                $events[$j]['resourceId'] = $field['id'];                   
                $events[$j]['start'] = $closeTime;
                $events[$j]['end'] = $openTime;
                $events[$j]['rendering'] = 'background';
                $events[$j]['color'] = '#c1c1c1';
                $events[$j]['overlap'] = false;
                $events[$j]['ranges'] = array(array('start' => '2010-01-01', 'end' => '9999-01-01'));
                $j++;
            }
            else if($dateTimeCloseTime > $dateTimeOpenTime)
            {                
                $events[$j]['resourceId'] = $field['id'];                   
                $events[$j]['start'] = $closeTime;
                $events[$j]['end'] = '23:59:59';
                $events[$j]['rendering'] = 'background';
                $events[$j]['color'] = '#c1c1c1';
                $events[$j]['overlap'] = false;
                $events[$j]['ranges'] = array(array('start' => '2010-01-01', 'end' => '9999-01-01'));
                $j++;
                
                if(intval(date('G', strtotime($reservation->open_time))) > 0)
                {
                    $events[$j]['resourceId'] = $field['id'];                   
                    $events[$j]['start'] = '00:00:00';
                    $events[$j]['end'] = $openTime;
                    $events[$j]['rendering'] = 'background';
                    $events[$j]['color'] = '#c1c1c1';
                    $events[$j]['overlap'] = false;
                    $events[$j]['ranges'] = array(array('start' => '2010-01-01', 'end' => '9999-01-01'));
                    $j++;
                }
                
            }

            foreach($reservation->holidays as $holiday)
            {

                if($holiday->avalible == '0')
                {
                    $holiday_start = date('Y-m-d H:i:s', strtotime("$holiday->start_date $holiday->start_time"));
                    $holiday_end = date('Y-m-d H:i:s', strtotime("$holiday->end_date $holiday->end_time"));

                    if(new Datetime($holiday->start_time) >= new Datetime($holiday->end_time))
                        $holiday_end =  date('Y-m-d H:i:s', strtotime("$holiday->end_date $holiday->end_time" . "+1 day"));
                    $events[$j]['resourceId'] = $field['id'];
                    $events[$j]['start'] = $holiday_start;
                    $events[$j]['end'] = $holiday_end;
                    $events[$j]['rendering'] = 'background';
                    $events[$j]['color'] = '#c1c1c1';
                    $events[$j]['overlap'] = false;
                    $events[$j]['selectable'] = false;
                    $events[$j]['ranges'] = array(array('start' => date('Y-m-d', strtotime($holiday->start_date . "-1 day")), 'end' => date('Y-m-d', strtotime($holiday->end_date . "+1 day"))));
                    $j++;
                }

            }                     
            
            $i++;

            foreach($field->reservation as $reserv)
            {
                $events[$j]['id'] = $reserv['id'];
                $events[$j]['resourceId'] = $reserv['field_id'];
                $events[$j]['field_price'] = $reserv['field_price'];
                $events[$j]['water_price'] = $reserv['water_price'];
                $events[$j]['supplement_price'] = $reserv['supplement_price'];
                $events[$j]['discount_price'] = $reserv['discount_price'];
                $events[$j]['start'] = $reserv['start_time'];
                $events[$j]['end'] = $reserv['end_time'];
                $events[$j]['title'] = $reserv['customer']['nickname'] . '_' . $reserv['customer']['mobile_number'];                
                $events[$j]['color'] = $reserv['background_color'];
                $events[$j]['description'] = $reserv['note'];
                $events[$j]['ranges'] = array(array('start' =>  date('Y-m-d', strtotime($reserv['start_time'] . "-1 day")), 'end' => date('Y-m-d', strtotime($reserv['end_time'] . "+1 day"))));
                if($reserv['status'] == '2')
                    $events[$j]['borderColor'] = '#54ff78';
                else if($reserv['status'] == '1')
                    $events[$j]['borderColor'] = '#fff662';
                else if($reserv['status'] == '99')
                    $events[$j]['borderColor'] = 'red';
                $j++;
            }

            

            foreach($field->tmp_field_price as $field_price)
            {
                $dow = array();

                if(strpos($field_price->day,'Holiday') !== false)
                {
                    foreach($reservation->holidays as $holiday)
                    {

                        if($holiday->avalible == '1')
                        {             
                            $holiday_start = date('Y-m-d H:i:s', strtotime("$holiday->start_date $holiday->start_time"));
                            $holiday_end = date('Y-m-d H:i:s', strtotime("$holiday->end_date $holiday->end_time"));

                            $start_time = $field_price['start_time'];
                            $end_time = $field_price['end_time'];

                            $dateTimeStarttime = date('Y-m-d H:i:s', strtotime("$holiday->start_date $start_time"));
                            $dateTimeEndtime = date('Y-m-d H:i:s', strtotime("$holiday->start_date $end_time"));


                            if($dateTimeStarttime > $dateTimeEndtime)
                                $dateTimeEndtime = date('Y-m-d H:i:s', strtotime("$holiday->start_date $end_time" . "+1 day"));

                            $events[$j]['start'] = $dateTimeStarttime;
                            $events[$j]['end'] = $dateTimeEndtime;
                            $events[$j]['resourceId'] = $field_price['field_id'];
                            $events[$j]['title'] = $field_price['price']; 
                            $events[$j]['rendering'] = 'background';
                            $events[$j]['color'] = $field_price['set_color'];
                            $events[$j]['ranges'] = array(array('start' => date('Y-m-d', strtotime($holiday->start_date . "-1 day")), 'end' => date('Y-m-d', strtotime($holiday->end_date . "+1 day"))));
                            $j++;
                        }                       
                    }
                }
                else
                {
                    if(strpos($field_price->day,'Sun') !== false)                
                        array_push($dow, 0);
                    
                    if(strpos($field_price->day,'Mon') !== false)
                        array_push($dow, 1);
                    
                    if(strpos($field_price->day,'Tue') !== false)
                        array_push($dow, 2);
                    
                    if(strpos($field_price->day,'Wen') !== false)
                        array_push($dow, 3);
                    
                    if(strpos($field_price->day,'Thu') !== false)
                        array_push($dow, 4);

                    if(strpos($field_price->day,'Fri') !== false)
                        array_push($dow, 5);

                    if(strpos($field_price->day,'Sat') !== false)
                        array_push($dow, 6);
                    
                    $start_time = $field_price['start_time'];
                    $end_time = $field_price['end_time'];

                    $dateTimeStarttime = new DateTime($start_time);
                    $dateTimeEndtime = new DateTime($end_time);
                    
                    if($dateTimeEndtime < $dateTimeStarttime)
                    {
                        $events[$j]['start'] = $field_price['start_time'];
                        $events[$j]['end'] = intval(date('G', strtotime($field_price['end_time']))) + 24 . ':00';
                        
                    }
                    else
                    {
                        $events[$j]['start'] = $field_price['start_time'];
                        $events[$j]['end'] = $field_price['end_time'];
                        
                    }
                    $events[$j]['dow'] = $dow;
                    $events[$j]['resourceId'] = $field_price['field_id'];
                    $events[$j]['title'] = $field_price['price']; 
                    $events[$j]['rendering'] = 'background';
                    $events[$j]['color'] = $field_price['set_color'];
                    $events[$j]['ranges'] = $holidays2;//array(array('start' => '2017-01-01', 'end' => '2017-12-05'), array('start' => '2017-12-05', 'end' => '2017-12-31'));
                    $j++;
                   
                }
                
                
                
                
// ============================================================================================================================================
                // $begin = new DateTime( $field_price->start_date );
                // $end = new DateTime( $field_price->end_date );
                // $end->modify('+1 day');

                // $interval = DateInterval::createFromDateString('1 day');
                // $period = new DatePeriod($begin, $interval, $end);

                // foreach ( $period as $dt )
                // {
                //     $date = $dt->format( "Y-m-d" );            
                //     $start_time = $field_price['start_time'];
                //     $end_time = $field_price['end_time'];
                    
                //     $dateTimeStarttime = new DateTime($start_time);
                //     $dateTimeEndtime = new DateTime($end_time);

                //     if ($dateTimeStarttime < $dateTimeOpenTime)                    
                //         $events[$j]['start'] = date('Y-m-d H:i:s', strtotime("$date $start_time" . "+1 days"));
                //     else
                //         $events[$j]['start'] = date('Y-m-d H:i:s', strtotime("$date $start_time"));

                //     if (($dateTimeEndtime < $dateTimeOpenTime) || (($dateTimeStarttime == $dateTimeEndtime) && ($dateTimeOpenTime == $dateTimeCloseTime) || $dateTimeEndtime < $dateTimeStarttime ))                    
                //         $events[$j]['end'] = date('Y-m-d H:i:s', strtotime("$date $end_time" . "+1 days"));
                //     else
                //         $events[$j]['end'] = date('Y-m-d H:i:s', strtotime("$date $end_time"));

                //     $events[$j]['resourceId'] = $field_price['field_id'];
                //     $events[$j]['title'] = $field_price['price']; 
                //     $events[$j]['rendering'] = 'background';
                //     $events[$j]['color'] = $field_price['set_color'];
                //     $j++;

                // }
// ==================================================================================================================================================        
            }  
        }

        return view('pages.reservation', compact('stadium', 'resource', 'reservation', 'events', 'openTime', 'closeTime', 'holidays2'));
    }

    public function addField(Request $request, $stadium)
    {
        $rules = array(
            'title' => 'required|max:50',
            'detail' => 'max:200'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) 
        {                
            Session::flash('error_msg', 'ไม่สามารถเพิ่มสนามได้!');
            return Redirect::to('/'. $stadium .'/reservation')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
        else
        {
            $field = New Field();
            $field->name	= $request->input('title');
            $field->detail  = $request->input('detail');
            if($request->input('add_status'))
                $field->status  = 1;
            else
                $field->status  = 0;
            $field->stadium_id = Auth::user()->stadium_id;
            $field->created_by = Auth::user()->id;
            $field->save();
            Session::flash('success_msg', 'เพิ่มสนามเรียบร้อยแล้ว!');
            return Redirect::to('/'. $stadium .'/reservation');
        }
    }

    public function editField(Request $request, $stadium)
    {
        $field = Field::where('id', $request->input('field_id'))->first();

        if(count($field) > 0)
        {
            $rules = array(
                'title' => 'required|max:50',
                'detail' => 'max:200'
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
            {                
                Session::flash('error_msg', 'ไม่สามารถแก้ไขสนามได้!');
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withErrors($validator)
                    ->withInput(Input::except('password'));
            }
            else
            {
                $field->name	= $request->input('title');
                $field->detail  = $request->input('detail');
                if($request->input('edit_status'))
                    $field->status  = 1;
                else 
                    $field->status  = 0;
                $field->updated_by = Auth::user()->id;
                $field->save();
                Session::flash('success_msg', 'แก้ไขสนามเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium .'/reservation');
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่พบข้อมูลสนามในฐานข้อมูล! กรุณาสร้างข้อมูลสนาม');
            return Redirect::to('/'. $stadium .'/reservation')
                ->withInput(Input::except('password'));
        }
    }

    public function deleteField(Request $request, $stadium)
    {
        $field = Field::where('id', $request->input('field_id'))->first();

        if(count($field) > 0)
        {
            $field->delete();
            Session::flash('success_msg', 'ลบสนามเรียบร้อยแล้ว!');
            return Redirect::to('/'. $stadium .'/reservation')
                ->withInput(Input::except('password'));
        }
        else
        {
            Session::flash('error_msg', 'ไม่พบข้อมูลสนามในฐานข้อมูล! กรุณาสร้างข้อมูลสนาม');
            return Redirect::to('/'. $stadium .'/reservation')
                ->withInput(Input::except('password'));
        }
    }

    function checkOpenTime($startTime, $endTime)
    {
        $stadium = Stadium::where('id', Auth::user()->stadium_id)->first();
        $openTime = new Datetime($stadium->open_time);
        $closeTime = new Datetime($stadium->close_time);

        if($openTime > $closeTime)
            $closeTime->modify('+1 day');
        if($startTime > $endTime)
            $endTime->modify('+1 day');

        if(($openTime <= $startTime) && ($closeTime >= $endTime) || ($openTime == $closeTime))
            return true;
        else
            return false;
    }

    public function addReserve(Request $request, $stadium)
    {
        
        $rules = array(
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i',
            'resource' => 'required',
            'mobile_number' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        

        if(!$validator->fails())
        {
            $customer = Customer::where('mobile_number', $request->input('mobile_number'))->first();
            
            if(count($customer) > 0)
            {                
                $this->addTmpCustomerStadium($customer);               
            }
            else
            {
                $customer = New Customer();                
                $customer->nickname  	= $request->input('nickname');
                $customer->mobile_number = $request->input('mobile_number');                
                $customer->created_by   = Auth::user()->id;
                $customer->save();
                $this->addTmpCustomerStadium($customer);                
            }
            
            $tmp_holiday = Holidays::where('stadium_id', Auth::user()->stadium_id)->where('avalible',1)->where('start_date', '<=', $request->input('hddEndDate'))->where('end_date', '>=', $request->input('hddStartDate'))->get();

            $startDay = date('D', strtotime($request->input('hddStartDate')));
            $endDay = date('D', strtotime($request->input('hddEndDate')));
            
            if(count($tmp_holiday) > 0)
                $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->where('tmp_field_price.day', 'like', '%Holiday%')->orderBy('start_time', 'asc')->get();
            elseif($startDay != $endDay)
                $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->where('tmp_field_price.day', 'like', '%' . $startDay . '%')->orWhere('tmp_field_price.day', 'like', '%' . $endDay . '%')->orderBy('start_time', 'asc')->get();
            else
                $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->where('tmp_field_price.day', 'like', '%' . $startDay . '%')->orderBy('start_time', 'asc')->get();
            
            //->where('start_date', '<', $request->input('hddEndDate'))->where('end_date', '>', $request->input('hddStartDate'))
            Log::info('$tmp_field_price '. json_encode($tmp_field_price));
            $reserveStarttime = new Datetime($request->input('startTime'));
            $reserveEndtime = new Datetime($request->input('endTime'));
            $reserveStartDate = new Datetime($request->input('hddStartDate'));
            $reserveEndDate = new Datetime($request->input('hddEndDate'));

            $open = $this->checkOpenTime($reserveStarttime, $reserveEndtime);
        
            if(!$open)
            {
                Session::flash('error_msg', 'ไม่สามารถเพิ่มการจองได้ กรุณาจองในช่วงเวลาที่สนามเปิดให้บริการ');
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withInput(Input::except('password'));
            }

            $start1 = new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'));
            $end1 = new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndtime->format('H:i:s'));
            $checkOverlap = Reservation::checkOverlap($request->input('hddResourceId'),$start1,$end1 )->get();
            
            $hicode = '';
            if(count($checkOverlap) == 0)
            {

                $over_flag = 0;            
                $reserved_flag = 0;
                $left_period_flag = 0;
                $done_flag = 0;
                $minutes_to_add = 1;            
                $ref_code = time();
                


                $stadium_data = Stadium::where('id', Auth::user()->stadium_id)->first();

                $totalDiscount = $this->promotion($reserveStarttime, $reserveEndtime, $reserveStartDate, $reserveEndDate, $stadium_data);

                
                foreach($tmp_field_price as $field_price)
                {
                    $fieldStarttime = new Datetime($field_price->start_time); 
                    $fieldEndtime = new Datetime($field_price->end_time);
                    
                    $reserveStartDate = new Datetime($request->input('hddStartDate'));
                    $reserveEndDate = new Datetime($request->input('hddEndDate'));
                    $tmpStart1 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $reserveStarttime->format('H:i:s'));
                    $tmpEnd1 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $reserveEndtime->format('H:i:s'));

                    $tmpStart2 = new DateTime($reserveStartDate->format('Y-m-d') . ' ' . $fieldStarttime->format('H:i:s'));
                    if($fieldEndtime > $fieldStarttime)                        
                        $tmpEnd2 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                    else if($fieldEndtime < $fieldStarttime)
                        $tmpEnd2 = new DateTime($reserveStartDate->modify('+1 day')->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                    else
                        $tmpEnd2 = new DateTime($reserveStartDate->modify('+2 day')->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                        
                    $reserveStartDate = new Datetime($request->input('hddStartDate'));
                    $reserveEndDate = new Datetime($request->input('hddEndDate'));
                                        
                    $minCost = ($field_price->price)/60;
                    if(($tmpStart1 <= $tmpEnd2) && ($tmpStart2 <= $tmpEnd1))
                    {                   
                        if($done_flag == 0 && $over_flag == 0 && ($tmpStart1 >= $tmpStart2 && $tmpStart1 < $tmpEnd2))
                        {
                            // $startDate = $reserveStartDate;
                            // $endDate = $reserveEndDate; 
                            if(($tmpEnd1 <= $tmpEnd2) && ($tmpStart1 < $tmpEnd1))
                            {
                                $startTime = $tmpStart1;
                                $endTime = $tmpEnd1;
                                $done_flag = 1;                           
                            }
                            else
                            {
                                $startTime = $tmpStart1;
                                $endTime = $tmpEnd2;                        
                                $tmpStarttime = $tmpEnd2;                   
                                $over_flag = 1;
                            }
                            if($endTime > $startTime)
                                $this->newReservation($customer, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $ref_code);
                            $reserved_flag = 1;                        
                            
                        }                
                        else if($done_flag == 0 && $over_flag == 1 && ($tmpStarttime >= $tmpStart2 && $tmpStarttime < $tmpEnd2))
                        {
                            // $startDate = $reserveStartDate;
                            // $endDate = $reserveEndDate; 
                            
                            if(($tmpEnd1 <= $tmpEnd2) && ($tmpStarttime < $tmpEnd1))
                            {   
                                $startTime = $tmpStarttime;
                                $endTime = $tmpEnd1;
                                $done_flag = 1;
                            }
                            else
                            {
                                $startTime = $tmpStarttime;
                                $endTime = $tmpEnd2;                        
                                $tmpStarttime = $tmpEnd2;                      
                                $over_flag = 1;
                            }
                            if($endTime > $startTime)
                                $this->newReservation($customer, $field_price, 0, $startTime, $endTime, $request, $minCost, $ref_code);
                            $reserved_flag = 1;
                        }        
                    }
                    
                }

                if($done_flag != 1)
                {
                    foreach($tmp_field_price as $field_price)
                    {
                        $fieldStarttime = new Datetime($field_price->start_time); 
                        $fieldEndtime = new Datetime($field_price->end_time);

                        if($left_period_flag == 0)
                            $reserveEndtime = new Datetime($request->input('endTime'));
                        else
                            $reserveEndtime = $tmpEndtime;
                        
                        $reserveStartDate = new Datetime($request->input('hddStartDate'));
                        $reserveEndDate = new Datetime($request->input('hddEndDate'));
                        $tmpStart1 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $reserveStarttime->format('H:i:s'));
                        $tmpEnd1 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $reserveEndtime->format('H:i:s'));

                        $tmpStart3 = new DateTime($reserveEndDate->format('Y-m-d') . ' ' . $fieldStarttime->format('H:i:s'));
                        if($fieldEndtime > $fieldStarttime)                        
                            $tmpEnd3 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                        else if($fieldEndtime < $fieldStarttime)
                            $tmpEnd3 = new DateTime($reserveEndDate->modify('+1 day')->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                        else
                            $tmpEnd3 = new DateTime($reserveEndDate->modify('+2 day')->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                            
                        $reserveStartDate = new Datetime($request->input('hddStartDate'));
                        $reserveEndDate = new Datetime($request->input('hddEndDate'));

                                            
                        $minCost = ($field_price->price)/60;
                        if(($tmpStart1 <= $tmpEnd3) && ($tmpStart3 <= $tmpEnd1))
                        {                            
                                                
                            if($done_flag == 0 && $over_flag == 0 && ($tmpStart1 >= $tmpStart3 && $tmpStart1 < $tmpEnd3))
                            {
                                // $startDate = $reserveStartDate;
                                // $endDate = $reserveEndDate; 
                                if(($tmpEnd1 <= $tmpEnd3) && ($tmpStart1 < $tmpEnd1))
                                {
                                    $startTime = $tmpStart1;
                                    $endTime = $tmpEnd1;
                                    $done_flag = 1;                           
                                }
                                else
                                {
                                    $startTime = $tmpStart1;
                                    $endTime = $tmpEnd3;                        
                                    $tmpStarttime = $tmpEnd3;                           
                                    $over_flag = 1;
                                }
                                if($endTime > $startTime)
                                    $this->newReservation($customer, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $ref_code);
                                $reserved_flag = 1;                        
                                
                            }                
                            else if($done_flag == 0 && $over_flag == 1 && ($tmpStarttime >= $tmpStart3 && $tmpStarttime < $tmpEnd3))
                            {
                                // $startDate = new Datetime($tmpStarttime->format('Y-m-d'));
                                // $endDate = $reserveEndDate; 
                                
                                if(($tmpEnd1 <= $tmpEnd3) && ($tmpStarttime < $tmpEnd1))
                                {   
                                    $startTime = $tmpStarttime;
                                    $endTime = $tmpEnd1;
                                    $done_flag = 1;
                                }
                                else
                                {
                                    $startTime = $tmpStarttime;
                                    $endTime = $tmpEnd3;                        
                                    $tmpStarttime = $tmpEnd3;                         
                                    $over_flag = 1;
                                }
                                if($endTime > $startTime)
                                    $this->newReservation($customer, $field_price, 0, $startTime, $endTime, $request, $minCost, $ref_code);
                                $reserved_flag = 1;
                            }
                        } 
                                        
                    }
                }
            }
            else
            {
                Session::flash('error_msg', 'ไม่สามารถจองได้ มีการจองซ้ำ');
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withInput(Input::except('password'));
            }
            if($reserved_flag == 1 && $done_flag == 1)
            {
                Session::flash('success_msg', 'เพิ่มการจองเข้าระบบเรียบร้อยแล้ว');
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withInput(Input::except('password'));
            }
            else
            {
                Session::flash('error_msg', 'ไม่สามารถจองได้ ไม่พบช่วงเวลานี้ ในฐานข้อมูล');
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withInput(Input::except('password'));
            }
            
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถจองได้');
            return Redirect::to('/'. $stadium .'/reservation')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
    }

    public function promotion($reserveStarttime, $reserveEndtime, $reserveStartDate, $reserveEndDate, $stadium_data)
    {
        $promotions = Promotions::where('stadium_id', Auth::user()->stadium_id)->get();
        $totalDiscount = 0;
        $over_flag_promo = 0;
        $minutes_to_add = 1;
        $minDiscount = 0;
        $discountType = '';
        foreach($promotions as $promotion)
        {
            $promo_startTime = new Datetime($promotion->start_time);
            $promo_endTime = new Datetime($promotion->end_time);

            $promo_startDate = new Datetime($promotion->start_date);
            $promo_endDate = new Datetime($promotion->end_date);

            $minDiscount = $promotion->discount/60;//$totalMinPromo
            
            if($promotion->discount_type == 'THB')            
                $discountType = 'THB';            
            else            
                $discountType = 'percent';
            

            if($over_flag_promo == 0 && ($reserveStarttime >= $promo_startTime && $reserveStarttime < $promo_endTime) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate))
            {
                if($reserveEndtime <= $promo_endTime)
                {
                    $startTime = $reserveStarttime;
                    $endTime = $reserveEndtime;                                                 
                }
                else
                {
                    $startTime = $reserveStarttime;                            
                    $endTime = $promo_endTime;
                    $tmpStarttime = $promo_endTime;
                    //$tmpStarttime->add(new DateInterval('PT' . $minutes_to_add . 'M'));                            
                    $over_flag_promo = 1;
                }
                if($discountType == 'THB')
                    $totalDiscount += $this->discounting($startTime, $endTime, $minDiscount);
                else
                    $totalDiscount = $promotion->discount;
                
            }
            else if($over_flag_promo == 1 && ($tmpStarttime >= $promo_startTime && $tmpStarttime < $promo_endTime) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate))
            {
                if($reserveEndtime <= $promo_endTime)
                {
                    $startTime = $tmpStarttime;
                    $endTime = $reserveEndtime;
                }
                else
                {
                    $startTime = $tmpStarttime;                            
                    $endTime = $promo_endTime;
                    $tmpStarttime = $promo_endTime;
                    //$tmpStarttime->add(new DateInterval('PT' . $minutes_to_add . 'M'));                            
                    $over_flag_promo = 1;
                }
                if($discountType == 'THB')
                    $totalDiscount += $this->discounting($startTime, $endTime, $minDiscount);
                else
                    $totalDiscount = $promotion->discount;
            }
            else if(($promo_endTime < new Datetime($stadium_data->open_time)) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate) && 
            (($reserveStarttime <= new Datetime("23:59") && $reserveStarttime >= new Datetime($stadium_data->open_time) && $promo_startTime <= new Datetime("23:59"))))
            {
                $mergeStart = new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'));
                $mergeEnd = new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndtime->format('H:i:s'));
                if($mergeStart >= new DateTime($reserveStartDate->format('Y-m-d') .' ' .$promo_startTime->format('H:i:s')))
                {   
                    if($mergeEnd <= new DateTime($reserveEndDate->format('Y-m-d') .' ' .$promo_endTime->format('H:i:s')))
                    {
                        $startTime = $mergeStart;
                        $endTime = $mergeEnd; 

                        if($discountType == 'THB')
                            $totalDiscount += $this->discounting($startTime, $endTime, $minDiscount);
                        else
                            $totalDiscount = $promotion->discount;
                    }
                }    
            }
            else if(($promo_endTime < new Datetime($stadium_data->open_time)) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate) && 
            ($reserveStarttime >= new Datetime("00:00") && $reserveStarttime < new Datetime($stadium_data->open_time) && $promo_startTime <= new Datetime("23:59") && $reserveStarttime < new Datetime($stadium_data->open_time)))
            {
                $tmpFieldStartDate = $reserveStartDate;
                $mergeStart = new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'));
                $mergeEnd = new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndtime->format('H:i:s'));

                if($mergeStart<= new DateTime($reserveEndDate->format('Y-m-d') .' ' .$promo_endTime->format('H:i:s')))                    
                {   
                    if($mergeEnd  >= new DateTime($tmpFieldStartDate->modify('-1 day')->format('Y-m-d') .' ' .$promo_startTime->format('H:i:s')))
                    {               
                        $startTime = $mergeStart;
                        $endTime = $mergeEnd; 

                        if($discountType == 'THB')
                            $totalDiscount += $this->discounting($startTime, $endTime, $minDiscount);
                        else
                            $totalDiscount = $promotion->discount;

                    }
                }          
            }
        }

        return array($totalDiscount, $discountType);
    }

    function discounting($startTime, $endTime, $minDiscount)
    {
        $totalDiscount = 0;
        $diffReserve = $startTime->diff($endTime)->format('%H:%i:%s');
        $arrTimeReserve = explode(":", $diffReserve);
        $totalMinReserve = (($arrTimeReserve[0] * 60) + $arrTimeReserve[1]);
        $totalDiscount += $totalMinReserve * $minDiscount;

        return $totalDiscount;
    }

    public function editReserve(Request $request, $stadium)
    {
        
        $rules = array(
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i',
            'resource' => 'required',
            'mobile_number' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if(!$validator->fails())
        {
            $customer = Customer::where('mobile_number', $request->input('mobile_number'))->first();
            
            if(count($customer) > 0)
            {                
                $this->addTmpCustomerStadium($customer);               
            }
            else
            {
                $customer = New Customer();                
                $customer->nickname  	= $request->input('nickname');
                $customer->mobile_number = $request->input('mobile_number');                
                $customer->created_by   = Auth::user()->id;
                $customer->save();
                $this->addTmpCustomerStadium($customer);                
            }

            $reservation = Reservation::where('id', $request->input('hddReserveId'))->first();            
            
            if(count($reservation) > 0)
            {
                $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->orderBy('start_time', 'asc')->get();
                $reserveStarttime = new Datetime($request->input('startTime'));
                $reserveEndtime = new Datetime($request->input('endTime'));
                $reserveStartDate = new Datetime($request->input('hddStartDate'));
                $reserveEndDate = new Datetime($request->input('hddEndDate'));
                //$reservationDay = Reservation::where('start_time', $request->input('hddDate'))->get();

                $open = $this->checkOpenTime($reserveStarttime, $reserveEndtime);
        
                if(!$open)
                {
                    Session::flash('error_msg', 'ไม่สามารถแก้ไขการจองได้ กรุณาจองในช่วงเวลาที่สนามเปิดให้บริการ');
                    return Redirect::to('/'. $stadium .'/reservation')
                        ->withInput(Input::except('password'));
                }

                $start1 = new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'));
                $end1 = new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndtime->format('H:i:s'));
                $checkOverlap = Reservation::checkOverlap($request->input('hddResourceId'), $start1, $end1, $reservation->id)->get();

                if(count($checkOverlap) == 0)
                {                
                    $over_flag = 0;
                    $left_period_flag = 0;
                    $done_flag = 0;
                    $minutes_to_add = 1;
                    $ref_code = $reservation->ref_code;

                    $stadium_data = Stadium::where('id', Auth::user()->stadium_id)->first();

                    $totalDiscount = $this->promotion($reserveStarttime, $reserveEndtime, $reserveStartDate, $reserveEndDate, $stadium_data);
                    
                    foreach($tmp_field_price as $field_price)
                    {
                        $fieldStarttime = new Datetime($field_price->start_time);
                        $fieldEndtime = new Datetime($field_price->end_time);

                        $fieldStartDate = new Datetime($field_price->start_date);
                        $fieldEndDate = new Datetime($field_price->end_date);

                        $reserveStarttime = new Datetime($request->input('startTime'));
                        if($left_period_flag == 0)
                            $reserveEndtime = new Datetime($request->input('endTime'));
                        else
                            $reserveEndtime = $tmpEndtime;
                        $reserveStartDate = new Datetime($request->input('hddStartDate'));
                        $reserveEndDate = new Datetime($request->input('hddEndDate'));
                        $tmpStart1 = new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'));
                        $tmpEnd1 = new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndtime->format('H:i:s'));
                        
                        $tmpStart2 = new DateTime($reserveStartDate->format('Y-m-d') . ' ' . $fieldStarttime->format('H:i:s'));
                        if($fieldEndtime > $fieldStarttime)                        
                            $tmpEnd2 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                        else if($fieldEndtime < $fieldStarttime)
                            $tmpEnd2 = new DateTime($reserveStartDate->modify('+1 day')->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                        else
                            $tmpEnd2 = new DateTime($reserveStartDate->modify('+2 day')->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));

                        $reserveStartDate = new Datetime($request->input('hddStartDate'));
                        $reserveEndDate = new Datetime($request->input('hddEndDate'));

                        $minCost = ($field_price->price)/60;
                        if(($tmpStart1 <= $tmpEnd2) && ($tmpStart2 <= $tmpEnd1))
                        {
                           if($done_flag == 0 && $over_flag == 0 && ($tmpStart1 >= $tmpStart2 && $tmpStart1 < $tmpEnd2))
                            {
                                $startDate = $reserveStartDate;
                                $endDate = $reserveEndDate; 
                                if(($tmpEnd1 <= $tmpEnd2) && ($tmpStart1 < $tmpEnd1))
                                {
                                    $startTime = $tmpStart1;
                                    $endTime = $tmpEnd1;
                                    $done_flag = 1;                           
                                }
                                else
                                {
                                    $startTime = $tmpStart1;
                                    $endTime = $tmpEnd2;                        
                                    $tmpStarttime = $tmpEnd2;                   
                                    $over_flag = 1;
                                }
                                if($endTime > $startTime)
                                    $this->editReservation($reservation, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost);                        
                                
                            }
                            else if($done_flag == 0 && $over_flag == 1 && ($tmpStarttime >= $tmpStart2 && $tmpStarttime < $tmpEnd2))
                            {
                                // $startDate = $reserveStartDate;
                                // $endDate = $reserveEndDate; 
                                
                                if(($tmpEnd1 <= $tmpEnd2) && ($tmpStarttime < $tmpEnd1))
                                {   
                                    $startTime = $tmpStarttime;
                                    $endTime = $tmpEnd1;
                                    $done_flag = 1;
                                }
                                else
                                {
                                    $startTime = $tmpStarttime;
                                    $endTime = $tmpEnd2;                        
                                    $tmpStarttime = $tmpEnd2;                      
                                    $over_flag = 1;
                                }
                                if($endTime > $startTime)
                                    $this->newReservation($customer, $field_price, 0, $startTime, $endTime, $request, $minCost, $ref_code);
                            }
                        } 
                    }

                    if($done_flag != 1)
                    {
                        foreach($tmp_field_price as $field_price)
                        {
                            $fieldStarttime = new Datetime($field_price->start_time); 
                            $fieldEndtime = new Datetime($field_price->end_time);

                            if($left_period_flag == 0)
                                $reserveEndtime = new Datetime($request->input('endTime'));
                            else
                                $reserveEndtime = $tmpEndtime;
                            
                            $reserveStartDate = new Datetime($request->input('hddStartDate'));
                            $reserveEndDate = new Datetime($request->input('hddEndDate'));
                            $tmpStart1 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $reserveStarttime->format('H:i:s'));
                            $tmpEnd1 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $reserveEndtime->format('H:i:s'));

                            $tmpStart3 = new DateTime($reserveEndDate->format('Y-m-d') . ' ' . $fieldStarttime->format('H:i:s'));
                            if($fieldEndtime > $fieldStarttime)                        
                                $tmpEnd3 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                            else if($fieldEndtime < $fieldStarttime)
                                $tmpEnd3 = new DateTime($reserveEndDate->modify('+1 day')->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                            else
                                $tmpEnd3 = new DateTime($reserveEndDate->modify('+2 day')->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                                
                            $reserveStartDate = new Datetime($request->input('hddStartDate'));
                            $reserveEndDate = new Datetime($request->input('hddEndDate'));

                                                
                            $minCost = ($field_price->price)/60;
                            if(($tmpStart1 <= $tmpEnd3) && ($tmpStart3 <= $tmpEnd1))
                            {                            
                                                    
                                if($done_flag == 0 && $over_flag == 0 && ($tmpStart1 >= $tmpStart3 && $tmpStart1 < $tmpEnd3))
                                {
                                    $startDate = $reserveStartDate;
                                    $endDate = $reserveEndDate; 
                                    if(($tmpEnd1 <= $tmpEnd3) && ($tmpStart1 < $tmpEnd1))
                                    {
                                        $startTime = $tmpStart1;
                                        $endTime = $tmpEnd1;
                                        $done_flag = 1;                           
                                    }
                                    else
                                    {
                                        $startTime = $tmpStart1;
                                        $endTime = $tmpEnd3;                        
                                        $tmpStarttime = $tmpEnd3;                           
                                        $over_flag = 1;
                                    }
                                    if($endTime > $startTime)
                                        $this->editReservation($reservation, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost);                     
                                    
                                }                
                                else if($done_flag == 0 && $over_flag == 1 && ($tmpStarttime >= $tmpStart3 && $tmpStarttime < $tmpEnd3))
                                {
                                    // $startDate = new Datetime($tmpStarttime->format('Y-m-d'));
                                    // $endDate = $reserveEndDate; 
                                    
                                    if(($tmpEnd1 <= $tmpEnd3) && ($tmpStarttime < $tmpEnd1))
                                    {   
                                        $startTime = $tmpStarttime;
                                        $endTime = $tmpEnd1;
                                        $done_flag = 1;
                                    }
                                    else
                                    {
                                        $startTime = $tmpStarttime;
                                        $endTime = $tmpEnd3;                        
                                        $tmpStarttime = $tmpEnd3;                         
                                        $over_flag = 1;
                                    }
                                    if($endTime > $startTime)
                                        $this->newReservation($customer, $field_price, 0, $startTime, $endTime, $request, $minCost, $ref_code);
                                }
                            } 
                                            
                        }
                    }
                }
                else
                {
                    Session::flash('error_msg', 'ไม่สามารถแก้ไขการจองได้ มีการจองซ้ำ');
                    return Redirect::to('/'. $stadium .'/reservation')
                        ->withInput(Input::except('password'));
                }

                Session::flash('success_msg', 'แก้ไขการจองเข้าระบบเรียบร้อยแล้ว');
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withInput(Input::except('password'));
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถแก้ไขการจองได้');
            return Redirect::to('/'. $stadium .'/reservation')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
    }

    public function paidReserve(Request $request, $stadium)
    {
        $reservation = Reservation::where('id', $request->input('hddReserveId'))->first();
        if(count($reservation) > 0)
        {
            try
            {
                $reservation->status = 2;
                $reservation->field_price = $request->input('field_price');
                $reservation->water_price = $request->input('water_price');
                $reservation->supplement_price = $request->input('supplement_price');
                $reservation->save();

                Session::flash('success_msg', 'บันทึกเรียบร้อย');
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withInput(Input::except('password'));
            }
            catch(Exception $e)
            {
                Session::flash('error_msg', 'ไม่สามารถบันทึกได้ พบข้อผิดพลาด' . $e->getMessage());
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withInput(Input::except('password'));
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่พบข้อมูลการจองในระบบ');
            return Redirect::to('/'. $stadium .'/reservation')
                ->withInput(Input::except('password'));
        }
    } 

    public function deleteReserve(Request $request, $stadium)
    {
        $reservation = Reservation::where('id', $request->input('reserve_id'))->first();            
            
        if(count($reservation) > 0)
        {
            try
            {
                $reservation->delete();
                Session::flash('success_msg', 'ลบการจองได้เรียบร้อยแล้ว');
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withInput(Input::except('password'));
            }
            catch(Exception $e)
            {
                Session::flash('error_msg', 'ไม่สามารถลบการจองได้ พบข้อผิดพลาด' . $e->getMessage());
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withInput(Input::except('password'));
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถลบการจองได้ ไม่พบข้อมูลการจองในระบบ');
            return Redirect::to('/'. $stadium .'/reservation')
                ->withInput(Input::except('password'));
        }
    }
    
    public function editReservation($reservation, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost)
    {
        try
        {      
            $mergeStart = $startTime;//new DateTime($startDate->format('Y-m-d') .' ' . $startTime->format('H:i:s'));
            $mergeEnd = $endTime;//new DateTime($endDate->format('Y-m-d') .' ' . $endTime->format('H:i:s'));

            $time = $mergeStart->diff($mergeEnd)->format('%H:%i:%s');
            $arrTime = explode(":", $time);
            $total_price = (($arrTime[0] * 60) + $arrTime[1]) * $minCost;

            $reservation->field_id = $request->input('hddResourceId');
            $reservation->start_time = $mergeStart;
            $reservation->end_time = $mergeEnd;
            $reservation->total_time = $time;
            $reservation->field_price = $total_price;
            if($totalDiscount[1] == 'THB')
            {
                $reservation->discount_price = $totalDiscount[0];
            }
            else
            {
                $reservation->discount_price = $total_price * ($totalDiscount[0]/100);
            }
            $reservation->background_color = $field_price->set_color;
            $reservation->note = $request->input('note');
            $reservation->updated_by = Auth::user()->id;
            $reservation->save();
        }
        catch(Exception $e)
        {
            Session::flash('error_msg', 'ไม่สามารถจองได้ พบข้อผิดพลาด' . $e->getMessage());
            return Redirect::to('/'. $stadium .'/reservation')
                ->withInput(Input::except('password'));
        }
    }

    function newReservation($customer, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $ref_code)
    {
        try
        { 
            $mergeStart = $startTime;//new DateTime($startDate->format('Y-m-d') .' ' . $startTime->format('H:i:s'));
            $mergeEnd = $endTime;//new DateTime($endDate->format('Y-m-d') .' ' . $endTime->format('H:i:s'));

            $time = $mergeStart->diff($mergeEnd)->format('%H:%i:%s');
            $arrTime = explode(":", $time);
            $total_price = (($arrTime[0] * 60) + $arrTime[1]) * $minCost;


            $reservation = new Reservation();
            $reservation->field_id = $request->input('hddResourceId');
            $reservation->customer_id = $customer->id;
            $reservation->start_time = $mergeStart;
            $reservation->end_time = $mergeEnd;
            $reservation->total_time = $time;
            $reservation->field_price = $total_price;
            if($totalDiscount[1] == 'THB')
            {
                $reservation->discount_price = $totalDiscount[0];
            }
            else
            {
                $reservation->discount_price = $total_price * ($totalDiscount[0]/100);
            }
            $reservation->background_color = $field_price->set_color;
            $reservation->ref_code = $ref_code;
            $reservation->note = $request->input('note');
            $reservation->created_by = Auth::user()->id;
            $reservation->save();
        }
        catch(Exception $e)
        {
            Session::flash('error_msg', 'ไม่สามารถจองได้ พบข้อผิดพลาด' . $e->getMessage());
            return Redirect::to('/'. $stadium .'/reservation')
                ->withInput(Input::except('password'));
        }
    }

    function addTmpCustomerStadium($customer)
    {
        $member_id = str_pad(Auth::user()->stadium_id, 3, "0", STR_PAD_LEFT) . str_pad($customer->id, 5, "0", STR_PAD_LEFT);           
        $tmp_customer_stadium = Tmp_Customer_Stadium::where('member_id', $member_id)->first();

        if(count($tmp_customer_stadium) <= 0)
        {   
            try
            {      
                $tmp_customer_stadium = new Tmp_Customer_Stadium();
                $tmp_customer_stadium->stadium_id   = Auth::user()->stadium_id;
                $tmp_customer_stadium->customer_id  = $customer->id;
                $tmp_customer_stadium->member_id    = $member_id;            
                $tmp_customer_stadium->created_by   = Auth::user()->id;
                $tmp_customer_stadium->save(); 
            }
            catch(Exception $e)
            {
                Session::flash('error_msg', 'ไม่สามารถจองได้ พบข้อผิดพลาด' . $e->getMessage());
                return Redirect::to('/'. $stadium .'/reservation')
                    ->withInput(Input::except('password'));
            }                   
        }
    }

    public function getCustomer(Request $request)
    {
        $customer = Customer::where('mobile_number', $request->input('_mobile_number'))->first();
        $result = '';
        if(count($customer) > 0)
            $result = $customer->nickname;

        return $result;
    }

    public function getHoliday(Request $request)
    {
        $holidays = Holidays::where('stadium_id', Auth::user()->stadium_id)->where('start_date', '<=', $request->input('_fullCalendarDate'))->where('end_date', '>=', $request->input('_fullCalendarDate'))->first();        
        $result = '';
        if(count($holidays) > 0)
        {
            $result = $holidays->name;
            if($holidays->avalible == 0)
                $result .= ' ปิดบริการ';
            else
                $result .= ' เปิดบริการ';
        }
            

        return $result;
    }
}
