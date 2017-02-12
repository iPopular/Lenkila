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
use Validator;
use Session;
use DateTime;
use DateInterval;
use DatePeriod;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class ReservationController extends Controller
{

    public function show(Request $request, $stadium)
    {
        //$fields = Field::where('stadium_id', Auth::user()->stadium_id)->where('status', '1')->get();
        $resource = array();
        $i = 0;
        
        $reservation = Stadium::where('id', Auth::user()->stadium_id)->first();
        $events = array();
        $j = 0;

        $openTime = intval(date('G', strtotime($reservation->open_time)));
        $closeTime = intval(date('G', strtotime($reservation->close_time)));
        $dateTimeOpenTime = new DateTime($reservation->open_time);
        $dateTimeCloseTime = new DateTime($reservation->close_time);

        //if($closeTime < $openTime)
           // $closeTime+=24;

        $openTime = $openTime . ':00:00';
        $closeTime = $closeTime . ':00:00';
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
                $j++;
            }
            else
            {
                $events[$j]['resourceId'] = $field['id'];                   
                $events[$j]['start'] = $closeTime;
                $events[$j]['end'] = $openTime;
                $events[$j]['rendering'] = 'background';
                $events[$j]['color'] = '#c1c1c1';
                $events[$j]['title'] = 'Close';
                $events[$j]['startEditable '] = false;
                $events[$j]['editable'] = false;
                $events[$j]['durationEditable '] = false;
                $events[$j]['resourceEditable'] = false;
                $events[$j]['overlap'] = false;
                $j++;
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

                // $dateTimeStarttimeEvent = new DateTime($reserv['start_time']);
                // $dateTimeEndtimeEvent = new DateTime($reserv['end_time']);

                
                // if ($dateTimeStarttimeEvent < $dateTimeOpenTime)                    
                //     $events[$j]['start'] = date('Y-m-d H:i:s', strtotime("$date" . $reserv['start_time'] . "-1 days"));
                // else
                //     $events[$j]['start'] = date('Y-m-d H:i:s', strtotime("$date" . $reserv['start_time'] ));

                // if ($dateTimeEndtimeEvent < $dateTimeOpenTime)                    
                //     $events[$j]['end'] = date('Y-m-d H:i:s', strtotime("$date" . $reserv['end_time'] . "+1 days"));
                // else
                //     $events[$j]['end'] = date('Y-m-d H:i:s', strtotime("$date" . $reserv['end_time'] ));

                // $startTimeIncrease = intval(date('H', strtotime($reserv['start_time']))) + 24;
                // $endTimeIncrease = intval(date('H', strtotime($reserv['end_time']))) + 24;
                // $startMinSec = date('i:s', strtotime($reserv['start_time']));
                // $endMinSec = date('i:s', strtotime($reserv['end_time']));

                
                // if (date('H:i:s', strtotime($reserv['start_time'])) < date('H:i:s', strtotime('07:00:00')))
                //     $events[$j]['start'] = '2017-02-11T00:00:00';//date('Y-m-d', strtotime($reserv['start_time'])) . 'T' . $startTimeIncrease . ':'.  $startMinSec;
                // else
                //     $events[$j]['start'] = $reserv['start_time'];

                // if(date('H:i:s', strtotime($reserv['end_time'])) < date('H:i:s', strtotime('07:00:00')))
                //     $events[$j]['end'] = '2017-02-11T03:00:00';//date('Y-m-d', strtotime($reserv['end_time'])) . 'T' . $endTimeIncrease . ':'.  $endMinSec;
                // else
                //     $events[$j]['end'] = $reserv['end_time'];

                $events[$j]['title'] = $reserv['customer']['nickname'] . '_' . $reserv['customer']['mobile_number'];                
                $events[$j]['color'] = $reserv['background_color'];
                $events[$j]['description'] = $reserv['note'];
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
                $begin = new DateTime( $field_price->start_date );
                $end = new DateTime( $field_price->end_date );
                $end->modify('+1 day');

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);

                foreach ( $period as $dt )
                {
                    $date = $dt->format( "Y-m-d" );            
                    $start_time = $field_price['start_time'];
                    $end_time = $field_price['end_time'];
                    
                    $dateTimeStarttime = new DateTime($start_time);
                    $dateTimeEndtime = new DateTime($end_time);

                    if ($dateTimeStarttime < $dateTimeOpenTime)                    
                        $events[$j]['start'] = date('Y-m-d H:i:s', strtotime("$date $start_time" . "+1 days"));
                    else
                        $events[$j]['start'] = date('Y-m-d H:i:s', strtotime("$date $start_time"));

                    if ($dateTimeEndtime < $dateTimeOpenTime)                    
                        $events[$j]['end'] = date('Y-m-d H:i:s', strtotime("$date $end_time" . "+1 days"));
                    else
                        $events[$j]['end'] = date('Y-m-d H:i:s', strtotime("$date $end_time"));

                    // if ($dateTimeCloseTime < $dateTimeOpenTime)
                    // {
                    //     if (($dateTimeStarttime < $dateTimeOpenTime) && ($dateTimeEndtime < $dateTimeOpenTime))
                    //         if(($dateTimeStarttime < $dateTimeCloseTime) && ($dateTimeEndtime <= $dateTimeCloseTime))
                    //         {
                    //             $events[$j]['start'] = date('Y-m-d H:i:s', strtotime("$date $start_time" . "+1 days"));
                    //             $events[$j]['end'] = date('Y-m-d H:i:s', strtotime("$date $end_time" . "+1 days"));
                    //         }
                    //         else if(($dateTimeStarttime > $dateTimeCloseTime) && ($dateTimeEndtime <= $dateTimeCloseTime))
                    //         {
                    //             $events[$j]['start'] = date('Y-m-d H:i:s', strtotime("$date $start_time"));
                    //             $events[$j]['end'] = date('Y-m-d H:i:s', strtotime("$date $end_time" . "+1 days"));
                    //         }

                    // }
                    // else
                    // {

                    // }

                    $events[$j]['resourceId'] = $field_price['field_id'];
                    $events[$j]['title'] = $field_price['price']; 
                    $events[$j]['rendering'] = 'background';
                    $events[$j]['color'] = $field_price['set_color'];
                    $j++;

                }
            }  
        }
        $THTTZ = new DateTimeZone('+0700');
        $date = new Datetime("00:00", $THTTZ );

        return view('pages.reservation', compact('stadium', 'resource', 'reservation', 'events', 'openTime', 'closeTime', 'date'));
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

    public function addReserve(Request $request, $stadium)
    {
        
        $rules = array(
            'startTime' => 'required',
            'endTime' => 'required',
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

            $THTTZ = new DateTimeZone('+0700');

            $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->orderBy('start_time', 'asc')->get();
            $reserveStarttime = new Datetime($request->input('startTime'), $THTTZ);
            $reserveEndttime = new Datetime($request->input('endTime'), $THTTZ);
            $reserveStartDate = new Datetime($request->input('hddStartDate'), $THTTZ);
            $reserveEndDate = new Datetime($request->input('hddEndDate'), $THTTZ);
            $over_flag = 0;            
            $reserved_flag = 0;
            $minutes_to_add = 1;            
            $ref_code = time();


            $stadium_data = Stadium::where('id', Auth::user()->stadium_id)->first();

            $totalDiscount = $this->promotion($reserveStarttime, $reserveEndttime, $reserveStartDate, $reserveEndDate, $stadium_data);

            
           
            foreach($tmp_field_price as $field_price)
            {
                $fieldStarttime = new Datetime($field_price->start_time, $THTTZ); 
                $fieldEndtime = new Datetime($field_price->end_time, $THTTZ);

                $fieldStartDate = new Datetime($field_price->start_date, $THTTZ);
                $fieldEndDate = new Datetime($field_price->end_date, $THTTZ);
                
                $minCost = ($field_price->price)/60;
                if($over_flag == 0 && ($reserveStarttime >= $fieldStarttime && $reserveStarttime <= $fieldEndtime) && ($reserveStartDate >= $fieldStartDate && $reserveEndDate <= $fieldEndDate))
                {
                    $startDate = $reserveStartDate;
                    $endDate = $reserveEndDate; 
                    if($reserveEndttime <= $fieldEndtime)
                    {
                        $startTime = $reserveStarttime;
                        $endTime = $reserveEndttime;                                   
                    }
                    else
                    {
                        $startTime = $reserveStarttime;
                        $endTime = $fieldEndtime;                        
                        $tmpStarttime = $fieldEndtime;
                        $tmpStarttime->add(new DateInterval('PT' . $minutes_to_add . 'M'));                            
                        $over_flag = 1;
                    }
                    if($endTime > $startTime)
                        $this->newReservation($customer, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $startDate, $endDate, $ref_code);
                    $reserved_flag = 1;                        
                    
                }                
                else if($over_flag == 1 && ($tmpStarttime >= $fieldStarttime && $tmpStarttime <= $fieldEndtime) && ($reserveStartDate >= $fieldStartDate && $reserveEndDate <= $fieldEndDate))
                {
                    $startDate = $reserveStartDate;
                    $endDate = $reserveEndDate; 
                    
                    if($reserveEndttime <= $fieldEndtime)
                    {   
                        $startTime = $tmpStarttime;
                        $endTime = $reserveEndttime; 
                    }
                    else
                    {
                        $startTime = $tmpStarttime;
                        $endTime = $fieldEndtime;                        
                        $tmpStarttime = $fieldEndtime;
                        $tmpStarttime->add(new DateInterval('PT' . $minutes_to_add . 'M'));                            
                        $over_flag = 1;
                    }
                    if($endTime > $startTime)
                        $this->newReservation($customer, $field_price, 0, $startTime, $endTime, $request, $minCost, $startDate, $endDate, $ref_code);
                    $reserved_flag = 1;
                }                    
                else if(($fieldEndtime < new Datetime($stadium_data->open_time, $THTTZ)) && ($reserveStartDate >= $fieldStartDate && $reserveEndDate <= $fieldEndDate) && 
                (($reserveStarttime <= new Datetime("23:59", $THTTZ) && $reserveStarttime >= new Datetime($stadium_data->open_time, $THTTZ) && $fieldStarttime <= new Datetime("23:59", $THTTZ))))
                {
                    if(new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'), $THTTZ) >= new DateTime($reserveStartDate->format('Y-m-d') .' ' .$fieldStarttime->format('H:i:s'), $THTTZ))
                    {   
                        if(new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndttime->format('H:i:s'), $THTTZ) <= new DateTime($reserveEndDate->format('Y-m-d') .' ' .$fieldEndtime->format('H:i:s'), $THTTZ))
                        {
                            $startDate = $reserveStartDate;
                            $endDate = $reserveEndDate;    
                    
                            $startTime = $reserveStarttime;
                            $endTime = $reserveEndttime;                            
                            $this->newReservation($customer, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $startDate, $endDate, $ref_code);
                            $reserved_flag = 1;  

                        }
                    }      
                            
                }
                else if(($fieldEndtime < new Datetime($stadium_data->open_time, $THTTZ)) && ($reserveStartDate >= $fieldStartDate && $reserveEndDate <= $fieldEndDate) && 
                ($reserveStarttime >= new Datetime("00:00", $THTTZ) && $reserveStarttime < new Datetime($stadium_data->open_time, $THTTZ) && $fieldStarttime <= new Datetime("23:59", $THTTZ) && $reserveStarttime < new Datetime($stadium_data->open_time, $THTTZ)))
                {
                    $tmpFieldStartDate = $reserveStartDate;

                    if(new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndttime->format('H:i:s'), $THTTZ) <= new DateTime($reserveEndDate->format('Y-m-d') .' ' .$fieldEndtime->format('H:i:s'), $THTTZ))                    
                    {   
                        if(new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'), $THTTZ) >= new DateTime($tmpFieldStartDate->modify('-1 day')->format('Y-m-d') .' ' .$fieldStarttime->format('H:i:s'), $THTTZ))
                        {
                            $startDate = new Datetime($request->input('hddStartDate'), $THTTZ);
                            $endDate = new Datetime($request->input('hddEndDate'), $THTTZ);   
                    
                            $startTime = $reserveStarttime;
                            $endTime = $reserveEndttime;                            
                            $this->newReservation($customer, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $startDate, $endDate, $ref_code);
                            $reserved_flag = 1;  

                        }
                    }          
                }    
                
                
            }
            if($reserved_flag == 1)
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

    public function promotion($reserveStarttime, $reserveEndttime, $reserveStartDate, $reserveEndDate, $stadium_data)
    {
        $promotions = Promotions::where('stadium_id', Auth::user()->stadium_id)->get();
        $totalDiscount = 0;
        $over_flag_promo = 0;
        $minutes_to_add = 1;
        $minDiscount = 0;
        $discountType = '';
        $THTTZ = new DateTimeZone('+0700');
        foreach($promotions as $promotion)
        {
            $promo_startTime = new Datetime($promotion->start_time, $THTTZ);
            $promo_endTime = new Datetime($promotion->end_time, $THTTZ);

            $promo_startDate = new Datetime($promotion->start_date, $THTTZ);
            $promo_endDate = new Datetime($promotion->end_date, $THTTZ);

            $minDiscount = $promotion->discount/60;//$totalMinPromo
            
            if($promotion->discount_type == 'THB')            
                $discountType = 'THB';            
            else            
                $discountType = 'percent';
            

            if($over_flag_promo == 0 && ($reserveStarttime >= $promo_startTime && $reserveStarttime <= $promo_endTime) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate))
            {
                if($reserveEndttime <= $promo_endTime)
                {
                    $startTime = $reserveStarttime;
                    $endTime = $reserveEndttime;                                                 
                }
                else
                {
                    $startTime = $reserveStarttime;                            
                    $endTime = $promo_endTime;
                    $tmpStarttime = $promo_endTime;
                    $tmpStarttime->add(new DateInterval('PT' . $minutes_to_add . 'M'));                            
                    $over_flag_promo = 1;
                }
                if($discountType == 'THB')
                    $totalDiscount += $this->discounting($startTime, $endTime, $minDiscount);
                else
                    $totalDiscount = $promotion->discount;
                
            }
            else if($over_flag_promo == 1 && ($tmpStarttime >= $promo_startTime && $tmpStarttime <= $promo_endTime) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate))
            {
                if($reserveEndttime <= $promo_endTime)
                {
                    $startTime = $tmpStarttime;
                    $endTime = $reserveEndttime;
                }
                else
                {
                    $startTime = $tmpStarttime;                            
                    $endTime = $promo_endTime;
                    $tmpStarttime = $promo_endTime;
                    $tmpStarttime->add(new DateInterval('PT' . $minutes_to_add . 'M'));                            
                    $over_flag_promo = 1;
                }
                if($discountType == 'THB')
                    $totalDiscount += $this->discounting($startTime, $endTime, $minDiscount);
                else
                    $totalDiscount = $promotion->discount;
            }
            else if(($promo_endTime < new Datetime($stadium_data->open_time, $THTTZ)) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate) && 
            (($reserveStarttime <= new Datetime("23:59", $THTTZ) && $reserveStarttime >= new Datetime($stadium_data->open_time, $THTTZ) && $promo_startTime <= new Datetime("23:59", $THTTZ))))
            {
                $mergeStart = new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'), $THTTZ);
                $mergeEnd = new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndttime->format('H:i:s'), $THTTZ);
                if($mergeStart >= new DateTime($reserveStartDate->format('Y-m-d') .' ' .$promo_startTime->format('H:i:s'), $THTTZ))
                {   
                    if($mergeEnd <= new DateTime($reserveEndDate->format('Y-m-d') .' ' .$promo_endTime->format('H:i:s'), $THTTZ))
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
            else if(($promo_endTime < new Datetime($stadium_data->open_time, $THTTZ)) && ($reserveStartDate >= $promo_startDate && $reserveEndDate <= $promo_endDate) && 
            ($reserveStarttime >= new Datetime("00:00", $THTTZ) && $reserveStarttime < new Datetime($stadium_data->open_time, $THTTZ) && $promo_startTime <= new Datetime("23:59", $THTTZ) && $reserveStarttime < new Datetime($stadium_data->open_time, $THTTZ)))
            {
                $tmpFieldStartDate = $reserveStartDate;
                $mergeStart = new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'), $THTTZ);
                $mergeEnd = new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndttime->format('H:i:s'), $THTTZ);

                if($mergeStart<= new DateTime($reserveEndDate->format('Y-m-d') .' ' .$promo_endTime->format('H:i:s'), $THTTZ))                    
                {   
                    if($mergeEnd  >= new DateTime($tmpFieldStartDate->modify('-1 day')->format('Y-m-d') .' ' .$promo_startTime->format('H:i:s'), $THTTZ))
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
            'startTime' => 'required',
            'endTime' => 'required',
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
                $THTTZ = new DateTimeZone('+0700');
                $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->orderBy('start_time', 'asc')->get();
                $reserveStarttime = new Datetime($request->input('startTime'), $THTTZ);
                $reserveEndttime = new Datetime($request->input('endTime'), $THTTZ);
                $reserveStartDate = new Datetime($request->input('hddStartDate'), $THTTZ);
                $reserveEndDate = new Datetime($request->input('hddEndDate'), $THTTZ);
                //$reservationDay = Reservation::where('start_time', $request->input('hddDate'))->get();
                $over_flag = 0;
                $minutes_to_add = 1;
                $ref_code = $reservation->ref_code;

                $stadium_data = Stadium::where('id', Auth::user()->stadium_id)->first();

                $totalDiscount = $this->promotion($reserveStarttime, $reserveEndttime, $reserveStartDate, $reserveEndDate, $stadium_data);
                
                foreach($tmp_field_price as $field_price)
                {
                    $fieldStarttime = new Datetime($field_price->start_time, $THTTZ);
                    $fieldEndtime = new Datetime($field_price->end_time, $THTTZ);

                    $fieldStartDate = new Datetime($field_price->start_date, $THTTZ);
                    $fieldEndDate = new Datetime($field_price->end_date, $THTTZ);

                    $minCost = ($field_price->price)/60;
                    if($over_flag == 0 && ($reserveStarttime >= $fieldStarttime && $reserveStarttime <= $fieldEndtime) && ($reserveStartDate >= $fieldStartDate && $reserveEndDate <= $fieldEndDate))
                    {
                        $startDate = $reserveStartDate;
                        $endDate = $reserveEndDate;                         
                        if($reserveEndttime <= $fieldEndtime)
                        {
                            $startTime = $reserveStarttime;
                            $endTime = $reserveEndttime;                            
                        }
                        else
                        {
                            $startTime = $reserveStarttime;                            
                            $endTime = $fieldEndtime;
                            $tmpStarttime = $fieldEndtime;
                            $tmpStarttime->add(new DateInterval('PT' . $minutes_to_add . 'M'));                            
                            $over_flag = 1;
                        }
                        if($endTime > $startTime)
                            $this->editReservation($reservation, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $startDate, $endDate);                        
                        
                    }
                    else if($over_flag == 1 && ($tmpStarttime >= $fieldStarttime && $tmpStarttime <= $fieldEndtime) && ($reserveStartDate >= $fieldStartDate && $reserveEndDate <= $fieldEndDate))
                    {
                        $startDate = $reserveStartDate;
                        $endDate = $reserveEndDate;
                        if($reserveEndttime <= $fieldEndtime)
                        {
                            $startTime = $tmpStarttime;
                            $endTime = $reserveEndttime;
                        }
                        else
                        {
                            $startTime = $tmpStarttime;
                            $endTime = $fieldEndtime;
                            $tmpStarttime = $fieldEndtime;
                            $tmpStarttime->add(new DateInterval('PT' . $minutes_to_add . 'M'));                            
                            $over_flag = 1;
                        }
                        if($endTime > $startTime)
                            $this->newReservation($customer, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $startDate, $endDate, $ref_code);
                                                
                    }
                    else if(($fieldEndtime < new Datetime($stadium_data->open_time, $THTTZ)) && ($reserveStartDate >= $fieldStartDate && $reserveEndDate <= $fieldEndDate) && 
                    (($reserveStarttime <= new Datetime("23:59", $THTTZ) && $reserveStarttime >= new Datetime($stadium_data->open_time, $THTTZ) && $fieldStarttime <= new Datetime("23:59", $THTTZ))))
                    {
                        if(new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'), $THTTZ) >= new DateTime($reserveStartDate->format('Y-m-d') .' ' .$fieldStarttime->format('H:i:s'), $THTTZ))
                        {   
                            if(new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndttime->format('H:i:s'), $THTTZ) <= new DateTime($reserveEndDate->format('Y-m-d') .' ' .$fieldEndtime->format('H:i:s'), $THTTZ))
                            {
                                $startDate = $reserveStartDate;
                                $endDate = $reserveEndDate;    
                        
                                $startTime = $reserveStarttime;
                                $endTime = $reserveEndttime;                            
                                $this->editReservation($reservation, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $startDate, $endDate);

                            }
                        }      
                                
                    }
                    else if(($fieldEndtime < new Datetime($stadium_data->open_time, $THTTZ)) && ($reserveStartDate >= $fieldStartDate && $reserveEndDate <= $fieldEndDate) && 
                    ($reserveStarttime >= new Datetime("00:00", $THTTZ) && $reserveStarttime < new Datetime($stadium_data->open_time, $THTTZ) && $fieldStarttime <= new Datetime("23:59", $THTTZ) && $reserveStarttime < new Datetime($stadium_data->open_time, $THTTZ)))
                    {
                        $tmpFieldStartDate = $reserveStartDate;

                        if(new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndttime->format('H:i:s'), $THTTZ) <= new DateTime($reserveEndDate->format('Y-m-d') .' ' .$fieldEndtime->format('H:i:s'), $THTTZ))                    
                        {   
                            if(new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'), $THTTZ) >= new DateTime($tmpFieldStartDate->modify('-1 day')->format('Y-m-d') .' ' .$fieldStarttime->format('H:i:s'), $THTTZ))
                            {
                                $startDate = new Datetime($request->input('hddStartDate'), $THTTZ);
                                $endDate = new Datetime($request->input('hddEndDate'), $THTTZ);   
                        
                                $startTime = $reserveStarttime;
                                $endTime = $reserveEndttime;                            
                                $this->editReservation($reservation, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $startDate, $endDate);

                            }
                        }          
                    } 
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
    
    public function editReservation($reservation, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $startDate, $endDate)
    {
        try
        {      
            $mergeStart = new DateTime($startDate->format('Y-m-d') .' ' . $startTime->format('H:i:s'));
            $mergeEnd = new DateTime($endDate->format('Y-m-d') .' ' . $endTime->format('H:i:s'));

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

    function newReservation($customer, $field_price, $totalDiscount, $startTime, $endTime, $request, $minCost, $startDate, $endDate, $ref_code)
    {
        try
        { 
            $mergeStart = new DateTime($startDate->format('Y-m-d') .' ' . $startTime->format('H:i:s'));
            $mergeEnd = new DateTime($endDate->format('Y-m-d') .' ' . $endTime->format('H:i:s'));

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
}
