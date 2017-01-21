<?php

namespace App\Http\Controllers;

use Auth;
use App\Customer as Customer;
use App\Field as Field;
use App\User as User;
use App\Reservation as Reservation;
use App\Tmp_Field_Price as Tmp_Field_Price;
use App\Tmp_Customer_Stadium as Tmp_Customer_Stadium;
use App\Stadium as Stadium;
use Validator;
use Session;
use DateTime;
use DateInterval;
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
        foreach($reservation->field as $field)
        {
            
            $resource[$i]['id'] = $field['id'];
            $resource[$i]['title'] = $field['name'];
            $resource[$i]['detail'] = $field['detail'];
            $resource[$i]['status'] = $field['status'];
            // if($field['status'] == 1)
            // {
            //     $resource[$i]['businessHours']['start'] ='08:00';
            //     $resource[$i]['businessHours']['end'] ='23:00';
            //     $resource[$i]['businessHours']['dow'] =6;
            // }
            // else
            // {
            //     $resource[$i]['businessHours']['start'] ='10:00';
            //     $resource[$i]['businessHours']['end'] = '20:00';
            //     $resource[$i]['businessHours']['dow'] =5;
            // }
            $i++;

            foreach($field->reservation as $reserv)
            {
                $events[$j]['id'] = $reserv['id'];
                $events[$j]['resourceId'] = $reserv['field_id'];
                $events[$j]['field_price'] = $reserv['field_price'];
                $events[$j]['water_price'] = $reserv['water_price'];
                $events[$j]['supplement_price'] = $reserv['supplement_price'];
                $events[$j]['start'] = $reserv['start_time'];
                $events[$j]['end'] = $reserv['end_time'];
                $events[$j]['title'] = $reserv['customer']['nickname'] . '_' . $reserv['customer']['mobile_number'];                
                $events[$j]['color'] = $reserv['background_color'];
                $events[$j]['description'] = $reserv['note'];
                $j++;
            }
                        
            
        }

        return view('pages.reservation', compact('stadium', 'resource', 'reservation', 'events'));
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

            $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->orderBy('start_time', 'asc')->get();
            $reserveStarttime = new Datetime($request->input('startTime'));
            $reserveEndttime = new Datetime($request->input('endTime'));
            $date = new Datetime($request->input('hddDate'));
            $over_flag = 0;
            $reserved_flag = 0;
            $minutes_to_add = 1;
            $ref_code = time();
            
            foreach($tmp_field_price as $field_price)
            {
                $fieldStarttime = new Datetime($field_price->start_time);
                $fieldEndtime = new Datetime($field_price->end_time);
                $minCost = ($field_price->price)/60;
                if($over_flag == 0 && $reserveStarttime >= $fieldStarttime && $reserveStarttime <= $fieldEndtime)
                {
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

                    $this->newReservation($customer, $field_price, $startTime, $endTime, $request, $minCost, $date, $ref_code);
                    $reserved_flag = 1;                        
                    
                }
                else if($over_flag == 1 && $tmpStarttime >= $fieldStarttime && $tmpStarttime <= $fieldEndtime)
                {
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
                    $this->newReservation($customer, $field_price, $startTime, $endTime, $request, $minCost, $date, $ref_code);
                    $reserved_flag = 1;
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
                //check price before insert
                // SELECT * 
                // FROM `reservation` 
                // WHERE ('2017-01-09 09:00:00' > `start_time` and '2017-01-09 09:00:00' < `end_time` ) 
                // or ('2017-01-09 10:30:00' > `start_time` and '2017-01-09 10:30:00' < `end_time`) 
                $tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->orderBy('start_time', 'asc')->get();
                $reserveStarttime = new Datetime($request->input('startTime'));
                $reserveEndttime = new Datetime($request->input('endTime'));
                $date = new Datetime($request->input('hddDate'));
                //$reservationDay = Reservation::where('start_time', $request->input('hddDate'))->get();
                $over_flag = 0;
                $minutes_to_add = 1;
                $ref_code = $reservation->ref_code;
                
                foreach($tmp_field_price as $field_price)
                {
                    $fieldStarttime = new Datetime($field_price->start_time);
                    $fieldEndtime = new Datetime($field_price->end_time);
                    $minCost = ($field_price->price)/60;
                    if($over_flag == 0 && $reserveStarttime >= $fieldStarttime && $reserveStarttime <= $fieldEndtime)
                    {
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

                        $this->editReservation($reservation, $field_price, $startTime, $endTime, $request, $minCost, $date);                        
                        
                    }
                    else if($over_flag == 1 && $tmpStarttime >= $fieldStarttime && $tmpStarttime <= $fieldEndtime)
                    {
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
                        $this->newReservation($customer, $field_price, $startTime, $endTime, $request, $minCost, $date, $ref_code);
                                                
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
    
    public function editReservation($reservation, $field_price, $startTime, $endTime, $request, $minCost, $date)
    {
        try
        {       
            $time = $startTime->diff($endTime)->format('%H:%i:%s');
            $arrTime = explode(":", $time);
            $total_price = (($arrTime[0] * 60) + $arrTime[1]) * $minCost;

            $mergeStart = new DateTime($date->format('Y-m-d') .' ' . $startTime->format('H:i:s'));
            $mergeEnd = new DateTime($date->format('Y-m-d') .' ' . $endTime->format('H:i:s'));

            $reservation->field_id = $request->input('hddResourceId');
            $reservation->start_time = $mergeStart;
            $reservation->end_time = $mergeEnd;
            $reservation->total_time = $time;
            $reservation->field_price = $total_price;
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

    function newReservation($customer, $field_price, $startTime, $endTime, $request, $minCost, $date, $ref_code)
    {
        try
        {       
            $time = $startTime->diff($endTime)->format('%H:%i:%s');
            $arrTime = explode(":", $time);
            $total_price = (($arrTime[0] * 60) + $arrTime[1]) * $minCost;

            $mergeStart = new DateTime($date->format('Y-m-d') .' ' . $startTime->format('H:i:s'));
            $mergeEnd = new DateTime($date->format('Y-m-d') .' ' . $endTime->format('H:i:s'));


            $reservation = new Reservation();
            $reservation->field_id = $request->input('hddResourceId');
            $reservation->customer_id = $customer->id;
            $reservation->start_time = $mergeStart;
            $reservation->end_time = $mergeEnd;
            $reservation->total_time = $time;
            $reservation->field_price = $total_price;
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
}
