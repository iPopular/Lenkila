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
            if((new Datetime($holiday->start_time) >= new Datetime($holiday->end_time)) && ($holiday->start_date == $holiday->end_date))   
                array_push($holidays, array('id' => $holiday->id, 'start' => date('Y-m-d H:i:s', strtotime("$holiday->start_date $holiday->start_time")), 'end' => date('Y-m-d H:i:s', strtotime("$holiday->end_date $holiday->end_time". "+1 day"))));// . "+1 day"
            else
                array_push($holidays, array('id' => $holiday->id, 'start' => date('Y-m-d H:i:s', strtotime("$holiday->start_date $holiday->start_time")), 'end' => date('Y-m-d H:i:s', strtotime("$holiday->end_date $holiday->end_time"))));
            
            //  if(new Datetime($holiday->start_time) >= new Datetime($holiday->end_time))   
            //     array_push($holidays, array('id' => $holiday->id, 'start' => date('Y-m-d', strtotime("$holiday->start_date")), 'end' => date('Y-m-d', strtotime("$holiday->end_date"))));// . "+1 day"
            // else
            //     array_push($holidays, array('id' => $holiday->id, 'start' => date('Y-m-d', strtotime("$holiday->start_date")), 'end' => date('Y-m-d', strtotime("$holiday->end_date"))));
            
        }
        if(count($holidays) > 1)
            $holidays2[0]['start'] = date('Y-m-d H:i:s', strtotime($holidays[count($holidays) - 1]['end'] . "-1 year"));
        else if(count($holidays) == 1)
            $holidays2[0]['start'] = date('Y-m-d H:i:s', strtotime($holidays[0]['end'] . "-1 year"));

        if(count($holidays) > 0)
            $holidays2[0]['end'] = date('Y-m-d H:i:s', strtotime($holidays[0]['start']));
        for($k = 1; $k < count($holidays); $k++)
        {

            $holidays2[$k]['start'] = $holidays[$k - 1]['end'];
            if($k < count($holidays))
                $holidays2[$k]['end'] = $holidays[$k]['start'];
            else
                $holidays2[$k]['end'] = date('Y-m-d H:i:s', strtotime($holidays[0]['start'] . "+1 year"));
        }
        $holidays3 = array();
        for ($k=0; $k < count($holidays2); $k++) {
            for ($l=-5; $l < 5; $l++) { 
                array_push($holidays3, array('start' => date('Y-m-d H:i:s', strtotime($holidays2[$k]['start'] . "+" .$l ."year")), 'end' => date('Y-m-d H:i:s', strtotime($holidays2[$k]['end'] . "+" .$l ."year"))));
            }            
        } 
        
        Log::info('holiday2: '. json_encode($holidays2));
        Log::info('holiday3: '. json_encode($holidays3));

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
                $events[$j]['color'] = 'rgba(150, 150, 150, 1)';
                $events[$j]['opacity'] = 1;
                $events[$j]['overlap'] = false;
                $events[$j]['avalible'] = '0';
                $events[$j]['ranges'] = array(array('start' => '2010-01-01', 'end' => '9999-01-01'));
                $j++;
            }
            else if($dateTimeCloseTime < $dateTimeOpenTime)
            {                

                $events[$j]['resourceId'] = $field['id'];                   
                $events[$j]['start'] = $closeTime;
                $events[$j]['end'] = $openTime;
                $events[$j]['rendering'] = 'background';
                $events[$j]['color'] = 'rgba(150, 150, 150, 1)';
                $events[$j]['opacity'] = 1;
                $events[$j]['overlap'] = false;
                $events[$j]['avalible'] = '0';
                $events[$j]['ranges'] = array(array('start' => '2010-01-01', 'end' => '9999-01-01'));
                $j++;
            }
            else if($dateTimeCloseTime > $dateTimeOpenTime)
            {                
                $events[$j]['resourceId'] = $field['id'];                   
                $events[$j]['start'] = $closeTime;
                $events[$j]['end'] = '23:59:59';
                $events[$j]['rendering'] = 'background';
                $events[$j]['color'] = 'rgba(150, 150, 150, 1)';
                $events[$j]['opacity'] = 1;
                $events[$j]['overlap'] = false;
                $events[$j]['avalible'] = '0';
                $events[$j]['ranges'] = array(array('start' => '2010-01-01', 'end' => '9999-01-01'));
                $j++;
                
                if(intval(date('G', strtotime($reservation->open_time))) > 0)
                {
                    $events[$j]['resourceId'] = $field['id'];                   
                    $events[$j]['start'] = '00:00:00';
                    $events[$j]['end'] = $openTime;
                    $events[$j]['rendering'] = 'background';
                    $events[$j]['color'] = 'rgba(150, 150, 150, 1)';
                    $events[$j]['opacity'] = 1;
                    $events[$j]['overlap'] = false;
                    $events[$j]['avalible'] = '0';
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

                    if((new Datetime($holiday->start_time) >= new Datetime($holiday->end_time)) && ($holiday->start_date == $holiday->end_date))
                        $holiday_end =  date('Y-m-d H:i:s', strtotime("$holiday->end_date $holiday->end_time" . "+1 day"));
                    $events[$j]['resourceId'] = $field['id'];
                    $events[$j]['start'] = $holiday_start;
                    $events[$j]['end'] = $holiday_end;
                    $events[$j]['rendering'] = 'background';
                    $events[$j]['color'] = 'rgba(150, 150, 150, 1)';
                    $events[$j]['opacity'] = 1;
                    $events[$j]['overlap'] = false;
                    $events[$j]['status'] = 0;
                    $events[$j]['avalible'] = '0';
                    //$events[$j]['ranges'] = array(array('start' => date('Y-m-d H:i:s', strtotime($holiday->start_date . "-1 day")), 'end' => date('Y-m-d', strtotime($holiday->end_date . "+1 day"))));
                    $events[$j]['ranges'] = array(array('start' => $holiday_start, 'end' => $holiday_end));
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
                $events[$j]['color'] = 'rgba(255, 255, 255, 0.15)';//$reserv['background_color'];
                $events[$j]['description'] = $reserv['note'];
                $events[$j]['avalible'] = '1';
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

                            $begin = new Datetime($holiday_start);
                            $end = new Datetime($holiday_end);
                            Log::info('begin: ' . date_format($begin, 'Y-m-d H:i:s') .', end:' . date_format($end, 'Y-m-d H:i:s'));
                            if($end <= $begin);
                                $end->modify('+1 day');
                            
                            if((new Datetime($holiday->start_time) >= new Datetime($holiday->end_time)) && ($holiday->start_date == $holiday->end_date))
                                $holiday_end =  date('Y-m-d H:i:s', strtotime("$holiday->end_date $holiday->end_time" . "+1 day"));

                            $interval = DateInterval::createFromDateString('1 day');
                            $period = new DatePeriod($begin, $interval, $end);
                            $start_time = new Datetime($field_price['start_time']);
                            $end_time = new Datetime($field_price['end_time']);
                            $str_starttime = $field_price['start_time'];
                            $str_endtime = $field_price['end_time'];
                            
                            foreach ( $period as $dt )
                            {  
                                $str_dt = date_format($dt, 'Y-m-d');
                                // $holiday_start = date('Y-m-d H:i:s', strtotime("$holiday->start_date $holiday->start_time"));
                                $dateTimeStarttime = date('Y-m-d H:i:s', strtotime("$str_dt $str_starttime"));//  date('Y-m-d H:i:s', strtotime("$holiday->start_date $start_time"));
                                $dateTimeEndtime = date('Y-m-d H:i:s', strtotime("$str_dt $str_endtime"));//date('Y-m-d H:i:s', strtotime("$holiday->start_date $end_time"));


                                if($dateTimeStarttime > $dateTimeEndtime)
                                    $dateTimeEndtime = date('Y-m-d H:i:s', strtotime("$str_dt $str_endtime" . "+1 day"));
                                
                                //Log::info('dateTimeStarttime: ' . date_format($dateTimeStarttime, 'Y-m-d H:i:s') .', dateTimeEndtime:' . date_format($dateTimeEndtime, 'Y-m-d H:i:s'));
                                $events[$j]['start'] = $dateTimeStarttime;
                                $events[$j]['end'] = $dateTimeEndtime;
                                $events[$j]['resourceId'] = $field_price['field_id'];
                                $events[$j]['title'] = $field_price['price']; 
                                $events[$j]['rendering'] = 'background';
                                $events[$j]['color'] = $field_price['set_color'];
                                $events[$j]['avalible'] = '1';
                                $events[$j]['ranges'] = array(array('start' => $holiday_start, 'end' => $holiday_end));
                                Log::info('holiday_start: ' . $holiday_start .', holiday_end:' . $holiday_end);
                                //Log::info('period: '. json_encode($period));
                                // $events[$j]['ranges'] = array(array('start' => date('Y-m-d', strtotime($holiday->start_date . "-1 day")), 'end' => date('Y-m-d', strtotime($holiday->end_date . "+1 day"))));
                                // Log::info($events[$j]);
                                $j++;
                            }
                        }                       
                    }
                }
                // else
                //{
                    if(strpos($field_price->day,'Sun') !== false)                
                        array_push($dow, 0);
                    
                    if(strpos($field_price->day,'Mon') !== false)
                        array_push($dow, 1);
                    
                    if(strpos($field_price->day,'Tue') !== false)
                        array_push($dow, 2);
                    
                    if(strpos($field_price->day,'Wed') !== false)
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
                    $events[$j]['avalible'] = '1';
                    if(count($holidays3) > 0)
                        $events[$j]['ranges'] = $holidays3;//array(array('start' => '2017-01-01', 'end' => '2017-12-05'), array('start' => '2017-12-04', 'end' => '2017-12-31'));////
                    else
                        $events[$j]['ranges'] = array(array('start' => '2010-01-01', 'end' => '9999-01-01'));
                    $j++;
                   
                //}
                
                
                
                
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
        $openTime = new DateTime($stadium->open_time);
        $closeTime = new DateTime($stadium->close_time);

        if($openTime > $closeTime)
            $closeTime->modify('+1 day');
  
        $startTime = new DateTime($openTime->format('Y-m-d') .' ' .$startTime->format('H:i:s'));
        $endTime = new DateTime($closeTime->format('Y-m-d') .' ' .$endTime->format('H:i:s'));

        $diffTime = $startTime->diff($endTime)->format('%d:%H:%i');
        $arrTime = explode(":", $diffTime);
        $diffTimeInt = ($arrTime[0] * 24 * 60) +($arrTime[1] * 60) + $arrTime[2];
        if($diffTimeInt > 1440)
            $startTime->modify('+1 day');
            
        // Log::info($diffTimeInt. '');
        // Log::info('$openTime: ' . date_format($openTime, 'Y-m-d H:i:s' ) .', $closeTime: '. date_format($closeTime, 'Y-m-d H:i:s' ) .', $startTime: '. date_format($startTime, 'Y-m-d H:i:s' ) .', $endTime: ' . date_format($endTime, 'Y-m-d H:i:s' ));


        $result = false;
        if(($openTime <= $startTime) && ($closeTime >= $endTime) || ($openTime == $closeTime))
        {
            $result = true;
        }
        else
        {
            if($diffTimeInt > 1440)
            {
                $startTime->modify('-1 day');
                $endTime->modify('-1 day');
                if(($openTime <= $startTime) && ($closeTime >= $endTime) || ($openTime == $closeTime))
                    $result = true;
                else
                    $result = false;
            }
            else
                $result = false;
            
        }
        return $result;
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
            //Log::info('$tmp_field_price '. json_encode($tmp_field_price));
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
                $totalPrice = 0;
                $totalDiscount = 0;        
                $ref_code = time();
                


                $stadium_data = Stadium::where('id', Auth::user()->stadium_id)->first();

                
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
                    {
                        $tmpEnd2 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                        $tmpEnd2->modify('+1 day');
                    }                        
                    else
                    {
                        $tmpEnd2 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                        $tmpEnd2->modify('+2 day');
                    }                       
                        
                    // $reserveStartDate = new Datetime($request->input('hddStartDate'));
                    // $reserveEndDate = new Datetime($request->input('hddEndDate'));
                                        
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
                            {
                                $thisPrice = $this->calTotalPrice($startTime, $endTime, $minCost);
                                $totalPrice += $thisPrice;    
                                $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                            }
                                                             
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
                            {
                                $thisPrice = $this->calTotalPrice($startTime, $endTime, $minCost);
                                $totalPrice += $thisPrice;    
                                $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                            }
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
                        {
                            $tmpEnd3 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                            $tmpEnd3->modify('+1 day');
                        }                            
                        else
                        {
                            $tmpEnd3 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                            $tmpEnd3->modify('+2 day');
                        }                            
                            
                        // $reserveStartDate = new Datetime($request->input('hddStartDate'));
                        // $reserveEndDate = new Datetime($request->input('hddEndDate'));

                                            
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
                                {
                                    $thisPrice = $this->calTotalPrice($startTime, $endTime, $minCost);
                                    $totalPrice += $thisPrice;    
                                    $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                                }  
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
                                {
                                    $thisPrice = $this->calTotalPrice($startTime, $endTime, $minCost);
                                    $totalPrice += $thisPrice;    
                                    $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                                }  
                                $reserved_flag = 1;
                            }
                        } 
                                        
                    }
                }
                
                if($totalPrice > 0 && $reserved_flag == 1 && $done_flag == 1)
                    $this->newReservation($customer, $totalDiscount, $start1, $end1, $request, $totalPrice, $ref_code);
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

    public function promotion($reserveStarttime, $reserveEndtime, $reserveStartDate, $reserveEndDate, $stadium_data, $totalPrice)
    {
        $promotions = Promotions::where('stadium_id', Auth::user()->stadium_id)->get();
        $totalDiscount = 0;
        $over_flag_promo = 0;
        $done_flag_promo = 0;
        $minutes_to_add = 1;
        $discount = 0;
        $discountType = '';
        // foreach($promotions as $promotion)
        // {
        //     $promo_startTime = new Datetime($promotion->start_time);
        //     $promo_endTime = new Datetime($promotion->end_time);

        //     $promo_startDate = new Datetime($promotion->start_date);
        //     $promo_endDate = new Datetime($promotion->end_date);

        //     $discount = $promotion->discount/60;//$totalMinPromo
            
        //     if($promotion->discount_type == 'THB')            
        //         $discountType = 'THB';            
        //     else            
        //         $discountType = 'percent';
            

        //     if($over_flag_promo == 0 && ($reserveStarttime >= $promo_startTime && $reserveStarttime < $promo_endTime) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate))
        //     {
        //         if($reserveEndtime <= $promo_endTime)
        //         {
        //             $startTime = $reserveStarttime;
        //             $endTime = $reserveEndtime;                                                 
        //         }
        //         else
        //         {
        //             $startTime = $reserveStarttime;                            
        //             $endTime = $promo_endTime;
        //             $tmpStarttime = $promo_endTime;
        //             //$tmpStarttime->add(new DateInterval('PT' . $minutes_to_add . 'M'));                            
        //             $over_flag_promo = 1;
        //         }
        //         $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType);
                
        //     }
        //     else if($over_flag_promo == 1 && ($tmpStarttime >= $promo_startTime && $tmpStarttime < $promo_endTime) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate))
        //     {
        //         if($reserveEndtime <= $promo_endTime)
        //         {
        //             $startTime = $tmpStarttime;
        //             $endTime = $reserveEndtime;
        //         }
        //         else
        //         {
        //             $startTime = $tmpStarttime;                            
        //             $endTime = $promo_endTime;
        //             $tmpStarttime = $promo_endTime;
        //             //$tmpStarttime->add(new DateInterval('PT' . $minutes_to_add . 'M'));                            
        //             $over_flag_promo = 1;
        //         }
        //         $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType);
        //     }
        //     else if(($promo_endTime < new Datetime($stadium_data->open_time)) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate) && 
        //     (($reserveStarttime <= new Datetime("23:59") && $reserveStarttime >= new Datetime($stadium_data->open_time) && $promo_startTime <= new Datetime("23:59"))))
        //     {
        //         $mergeStart = new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'));
        //         $mergeEnd = new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndtime->format('H:i:s'));
        //         if($mergeStart >= new DateTime($reserveStartDate->format('Y-m-d') .' ' .$promo_startTime->format('H:i:s')))
        //         {   
        //             if($mergeEnd <= new DateTime($reserveEndDate->format('Y-m-d') .' ' .$promo_endTime->format('H:i:s')))
        //             {
        //                 $startTime = $mergeStart;
        //                 $endTime = $mergeEnd; 
        //                 $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType);
        //             }
        //         }    
        //     }
        //     else if(($promo_endTime < new Datetime($stadium_data->open_time)) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate) && 
        //     ($reserveStarttime >= new Datetime("00:00") && $reserveStarttime < new Datetime($stadium_data->open_time) && $promo_startTime <= new Datetime("23:59") && $reserveStarttime < new Datetime($stadium_data->open_time)))
        //     {
        //         $tmpFieldStartDate = $reserveStartDate;
        //         $mergeStart = new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'));
        //         $mergeEnd = new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndtime->format('H:i:s'));

        //         if($mergeStart<= new DateTime($reserveEndDate->format('Y-m-d') .' ' .$promo_endTime->format('H:i:s')))                    
        //         {   
        //             if($mergeEnd  >= new DateTime($tmpFieldStartDate->modify('-1 day')->format('Y-m-d') .' ' .$promo_startTime->format('H:i:s')))
        //             {               
        //                 $startTime = $mergeStart;
        //                 $endTime = $mergeEnd; 
        //                 $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType);
        //             }
        //         }          
        //     }
        // }
        foreach($promotions as $promotion)
        {
            $promo_startTime = new Datetime($promotion->start_time);
            $promo_endTime = new Datetime($promotion->end_time);

            $promo_startDate = new Datetime($promotion->start_date);
            $promo_endDate = new Datetime($promotion->end_date);
            
            $tmpStart1 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $reserveStarttime->format('H:i:s'));
            $tmpEnd1 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $reserveEndtime->format('H:i:s'));

            $tmpStart2 = new DateTime($reserveStartDate->format('Y-m-d') . ' ' . $promo_startTime->format('H:i:s'));
            if($promo_endTime > $promo_startTime)                        
                $tmpEnd2 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $promo_endTime->format('H:i:s'));
            else if($promo_endTime < $promo_startTime)
            {
                $tmpEnd2 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $promo_endTime->format('H:i:s'));
                $tmpEnd2->modify('+1 day');
            }                
            else
            {
                $tmpEnd2 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $promo_endTime->format('H:i:s'));
                $tmpEnd2->modify('+2 day');
            }

            if($promotion->discount_type == 'THB')            
                $discountType = 'THB';            
            else            
                $discountType = 'percent';
                
                        
            $discount = $promotion->discount;
            if(($tmpStart1 <= $tmpEnd2) && ($tmpStart2 <= $tmpEnd1) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate))
            {  
                Log::info('$tmpStart1: '. date_format($tmpStart1,'Y-m-d H:i:s' ) .'$tmpStart2: '. date_format($tmpStart2,'Y-m-d H:i:s' )  . '$tmpEnd2: ' . date_format($tmpEnd2,'Y-m-d H:i:s' ) );                 
                if($done_flag_promo == 0 && $over_flag_promo == 0 && ($tmpStart1 >= $tmpStart2 && $tmpStart1 < $tmpEnd2))
                {
                      
                    if(($tmpEnd1 <= $tmpEnd2) && ($tmpStart1 < $tmpEnd1))
                    {
                        $startTime = $tmpStart1;
                        $endTime = $tmpEnd1;
                        $done_flag_promo = 1;                           
                    }
                    else
                    {
                        $startTime = $tmpStart1;
                        $endTime = $tmpEnd2;                        
                        $tmpStarttime = $tmpEnd2;                   
                        $over_flag_promo = 1;
                    }
                    Log::info('pass 2st if $startTime: ' . date_format($startTime,'Y-m-d H:i:s' )  . ', $endTime: '. date_format($endTime,'Y-m-d H:i:s' ) );
                    if($endTime > $startTime)
                        $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType, $reserveStarttime, $reserveEndtime);                                
                     
                    
                }                
                else if($done_flag_promo == 0 && $over_flag_promo == 1 && ($tmpStarttime >= $tmpStart2 && $tmpStarttime < $tmpEnd2))
                {
                    Log::info('pass 3st if');  
                    if(($tmpEnd1 <= $tmpEnd2) && ($tmpStarttime < $tmpEnd1))
                    {   
                        $startTime = $tmpStarttime;
                        $endTime = $tmpEnd1;
                        $done_flag_promo = 1;
                    }
                    else
                    {
                        $startTime = $tmpStarttime;
                        $endTime = $tmpEnd2;                        
                        $tmpStarttime = $tmpEnd2;                      
                        $over_flag_promo = 1;
                    }
                    if($endTime > $startTime)
                        $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType, $reserveStarttime, $reserveEndtime);    
                }
                else if($done_flag_promo == 0 && $over_flag_promo == 0 && ($tmpStart1 < $tmpStart2 && $tmpEnd1 > $tmpStart2))
                {
                    if(($tmpEnd1 <= $tmpEnd2) && ($tmpStart1 < $tmpEnd1))
                    {
                        $startTime = $tmpStart2;
                        $endTime = $tmpEnd1;
                        $done_flag_promo = 1;                           
                    }
                    else
                    {
                        $startTime = $tmpStart2;
                        $endTime = $tmpEnd2;                        
                        $tmpStarttime = $tmpEnd2;                   
                        $over_flag_promo = 1;
                    }
                    Log::info('pass 2st if $startTime: ' . date_format($startTime,'Y-m-d H:i:s' )  . ', $endTime: '. date_format($endTime,'Y-m-d H:i:s' ) );
                    if($endTime > $startTime)
                        $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType, $reserveStarttime, $reserveEndtime); 
                }       
            }
            
        }
        //**==============================================================================================================================//
        if($done_flag_promo != 1)
        {
            foreach($promotions as $promotion)
            {
                $promo_startTime = new Datetime($promotion->start_time);
                $promo_endTime = new Datetime($promotion->end_time);

                // if($left_period_flag == 0)
                //     $reserveEndtime = new Datetime($request->input('endTime'));
                // else
                //     $reserveEndtime = $tmpEndtime;
                
                $tmpStart1 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $reserveStarttime->format('H:i:s'));
                $tmpEnd1 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $reserveEndtime->format('H:i:s'));

                $tmpStart3 = new DateTime($reserveEndDate->format('Y-m-d') . ' ' . $promo_startTime->format('H:i:s'));
                if($promo_endTime > $promo_startTime)                        
                    $tmpEnd3 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $promo_endTime->format('H:i:s'));
                else if($promo_endTime < $promo_startTime)
                {
                    $tmpEnd3 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $promo_endTime->format('H:i:s'));
                    $tmpEnd3->modify('+1 day');
                }                    
                else
                {
                    $tmpEnd3 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $promo_endTime->format('H:i:s'));
                    $tmpEnd3->modify('+2 day');
                }
                    
                if($promotion->discount_type == 'THB')            
                    $discountType = 'THB';            
                else            
                    $discountType = 'percent';
                             
                $discount = $promotion->discount;
                if(($tmpStart1 <= $tmpEnd3) && ($tmpStart3 <= $tmpEnd1) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate))
                {                            
                    Log::info('foreach2 $tmpStart1: '. date_format($tmpStart1,'Y-m-d H:i:s' ) .'$tmpStart2: '. date_format($tmpStart2,'Y-m-d H:i:s' )  . '$tmpEnd2: ' . date_format($tmpEnd2,'Y-m-d H:i:s' ) );                    
                    if($done_flag_promo == 0 && $over_flag_promo == 0 && ($tmpStart1 >= $tmpStart3 && $tmpStart1 < $tmpEnd3))
                    {
                        // $startDate = $reserveStartDate;
                        // $endDate = $reserveEndDate; 
                        if(($tmpEnd1 <= $tmpEnd3) && ($tmpStart1 < $tmpEnd1))
                        {
                            $startTime = $tmpStart1;
                            $endTime = $tmpEnd1;
                            $done_flag_promo = 1;                           
                        }
                        else
                        {
                            $startTime = $tmpStart1;
                            $endTime = $tmpEnd3;                        
                            $tmpStarttime = $tmpEnd3;                           
                            $over_flag_promo = 1;
                        }
                        if($endTime > $startTime)
                            $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType, $reserveStarttime, $reserveEndtime);                         
                        
                    }                
                    else if($done_flag_promo == 0 && $over_flag_promo == 1 && ($tmpStarttime >= $tmpStart3 && $tmpStarttime < $tmpEnd3))
                    {
                        // $startDate = new Datetime($tmpStarttime->format('Y-m-d'));
                        // $endDate = $reserveEndDate; 
                        
                        if(($tmpEnd1 <= $tmpEnd3) && ($tmpStarttime < $tmpEnd1))
                        {   
                            $startTime = $tmpStarttime;
                            $endTime = $tmpEnd1;
                            $done_flag_promo = 1;
                        }
                        else
                        {
                            $startTime = $tmpStarttime;
                            $endTime = $tmpEnd3;                        
                            $tmpStarttime = $tmpEnd3;                         
                            $over_flag_promo = 1;
                        }
                        if($endTime > $startTime)
                            $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType, $reserveStarttime, $reserveEndtime);   

                    }
                    else if($done_flag_promo == 0 && $over_flag_promo == 0 && ($tmpStart1 < $tmpStart3 && $tmpEnd1 > $tmpEnd3))
                    {
                        if(($tmpEnd1 <= $tmpEnd3) && ($tmpStart1 < $tmpEnd1))
                        {
                            $startTime = $tmpStart3;
                            $endTime = $tmpEnd1;
                            $done_flag_promo = 1;                           
                        }
                        else
                        {
                            $startTime = $tmpStart3;
                            $endTime = $tmpEnd3;                        
                            $tmpStarttime = $tmpEnd3;                   
                            $over_flag_promo = 1;
                        }
                        Log::info('pass 2st if $startTime: ' . date_format($startTime,'Y-m-d H:i:s' )  . ', $endTime: '. date_format($endTime,'Y-m-d H:i:s' ) );
                        if($endTime > $startTime)
                            $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType, $reserveStarttime, $reserveEndtime); 
                    }
                } 
                                
            }
        }
        //**==============================================================================================================================

        return $totalDiscount;
    }

    function discounting($startTime, $endTime, $discount, $totalPrice, $discountType, $reserveStarttime, $reserveEndtime)
    {
        $totalDiscount = 0;
        $diffReserve = $startTime->diff($endTime)->format('%H:%i:%s');
        $arrTimeReserve = explode(":", $diffReserve);
        $totalMinDiscount = (($arrTimeReserve[0] * 60) + $arrTimeReserve[1]);

        // $tmpStart1 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $reserveStarttime->format('H:i:s'));
        // $tmpEnd1 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $reserveEndtime->format('H:i:s'));
        $diffTotalReserve = $reserveStarttime->diff($reserveEndtime)->format('%H:%i:%s');
        $arrTimeTotalReserve = explode(":", $diffTotalReserve);
        $totalMinReserve = (($arrTimeTotalReserve[0] * 60) + $arrTimeTotalReserve[1]);

        $minCost = $totalPrice/$totalMinReserve;
        $baseCostForDiscount = $minCost * $totalMinDiscount;
        if($discountType == "THB")
            $totalDiscount = $totalMinDiscount * ($discount/60);
        else        
            $totalDiscount = ($baseCostForDiscount * ($discount/100));
        // Log::info('$reserveStarttime '. date_format($reserveStarttime,'Y-m-d H:i:s' ) . ', $reserveEndtime ' . date_format($reserveEndtime,'Y-m-d H:i:s' ) );
        // Log::info('$totalMinReserve '. $totalMinReserve . ', $totalDiscount ' . $totalDiscount . ', $baseCostForDiscount ' . $baseCostForDiscount);

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
                $tmp_holiday = Holidays::where('stadium_id', Auth::user()->stadium_id)->where('avalible',1)->where('start_date', '<=', $request->input('hddEndDate'))->where('end_date', '>=', $request->input('hddStartDate'))->get();

                $startDay = date('D', strtotime($request->input('hddStartDate')));
                $endDay = date('D', strtotime($request->input('hddEndDate')));
                
                if(count($tmp_holiday) > 0)
                    $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->where('tmp_field_price.day', 'like', '%Holiday%')->orderBy('start_time', 'asc')->get();
                elseif($startDay != $endDay)
                    $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->where('tmp_field_price.day', 'like', '%' . $startDay . '%')->orWhere('tmp_field_price.day', 'like', '%' . $endDay . '%')->orderBy('start_time', 'asc')->get();
                else
                    $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->where('tmp_field_price.day', 'like', '%' . $startDay . '%')->orderBy('start_time', 'asc')->get();
                //$tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->orderBy('start_time', 'asc')->get();
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
                    $totalPrice = 0;
                    $totalDiscount = 0;

                    $stadium_data = Stadium::where('id', Auth::user()->stadium_id)->first();                    
                    
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
                        {
                            $tmpEnd2 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                            $tmpEnd2->modify('+1 day');
                        }                            
                        else
                        {
                            $tmpEnd2 = new DateTime($reserveStartDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                            $tmpEnd2->modify('+2 day');
                        }
                            

                        // $reserveStartDate = new Datetime($request->input('hddStartDate'));
                        // $reserveEndDate = new Datetime($request->input('hddEndDate'));

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
                                {
                                    $thisPrice = $this->calTotalPrice($startTime, $endTime, $minCost);
                                    $totalPrice += $thisPrice;    
                                    $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                                }                                                         
                                
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
                                {
                                    $thisPrice = $this->calTotalPrice($startTime, $endTime, $minCost);
                                    $totalPrice += $thisPrice;    
                                    $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                                }  
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
                            {
                                $tmpEnd3 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                                $tmpEnd3->modify('+1 day');
                            }                                
                            else
                            {
                                $tmpEnd3 = new DateTime($reserveEndDate->format('Y-m-d') .' ' . $fieldEndtime->format('H:i:s'));
                                $tmpEnd3->modify('+2 day');
                            }
                                
                                
                            // $reserveStartDate = new Datetime($request->input('hddStartDate'));
                            // $reserveEndDate = new Datetime($request->input('hddEndDate'));

                                                
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
                                    {
                                        $thisPrice = $this->calTotalPrice($startTime, $endTime, $minCost);
                                        $totalPrice += $thisPrice;    
                                        $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                                    }                      
                                    
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
                                    {
                                        $thisPrice = $this->calTotalPrice($startTime, $endTime, $minCost);
                                        $totalPrice += $thisPrice;    
                                        $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                                    }  
                                }
                            } 
                                            
                        }
                    }
                    //$totalDiscount = $this->promotion($reserveStarttime, $reserveEndtime, $reserveStartDate, $reserveEndDate, $stadium_data, $totalPrice);
                    if($totalPrice > 0 && $done_flag == 1)
                        $this->editReservation($reservation, $totalDiscount, $start1, $end1, $request, $totalPrice); 
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
    
    public function editReservation($reservation, $totalDiscount, $startTime, $endTime, $request, $totalPrice)
    {
        try
        {      
            $reservation->field_id = $request->input('hddResourceId');
            $reservation->start_time = $startTime;
            $reservation->end_time = $endTime;
            $reservation->total_time = $startTime->diff($endTime)->format('%H:%i:%s');
            $reservation->field_price = $totalPrice;
            // if($totalDiscount[1] == 'THB')
            // {
                $reservation->discount_price = $totalDiscount;
            // }
            // else
            // {
            //     $reservation->discount_price = $totalPrice * ($totalDiscount[0]/100);
            // }
            //$reservation->background_color = $field_price->set_color;
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

    function newReservation($customer, $totalDiscount, $startTime, $endTime, $request, $totalPrice, $ref_code)
    {
        try
        { 
            $reservation = new Reservation();
            $reservation->field_id = $request->input('hddResourceId');
            $reservation->customer_id = $customer->id;
            $reservation->start_time = $startTime;
            $reservation->end_time = $endTime;
            $reservation->total_time = $startTime->diff($endTime)->format('%H:%i:%s');
            $reservation->field_price = $totalPrice;
            // if($totalDiscount[1] == 'THB')
            // {
                $reservation->discount_price = $totalDiscount;
            // }
            // else
            // {
            //     $reservation->discount_price = $totalPrice * ($totalDiscount[0]/100);
            // }
            //$reservation->background_color = $field_price->set_color;
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

    public function calTotalPrice($startTime, $endTime, $minCost)
    {
        $time = $startTime->diff($endTime)->format('%H:%i:%s');
        $arrTime = explode(":", $time);
        $totalPrice = (($arrTime[0] * 60) + $arrTime[1]) * $minCost;

        return $totalPrice;
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
