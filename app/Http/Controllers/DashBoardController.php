<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Session;
use DateTime;
use Log;
use App\Stadium as Stadium;
use App\Tmp_Field_Price as Tmp_Field_Price;
use App\Promotions as Promotions;
use App\Holidays as Holidays;
use App\Reservation as Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class DashBoardController extends Controller
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

    public function show(Request $request, $stadium_name)
    {
        $stadium = Stadium::where('id', Auth::user()->stadium_id)->first();

        return view('pages.dashboard', compact('stadium_name', 'stadium')); 
    }

    public function addFieldPrice(Request $request, $stadium_name)
    {
        $rules = array(
            'field' => 'required|integer',
            'field_price' => 'required|integer',
            'start_time' => 'required',
            'end_time' => 'required',
            // 'start' => 'required|date_format:Y-m-d',
            // 'end' => 'required|date_format:Y-m-d',
        );

        $startTime = new Datetime($request->input('start_time'));
        $endTime = new Datetime($request->input('end_time'));
        // $startDate = new Datetime($request->input('start'));
        // $endDate = new Datetime($request->input('end'));
        
        $open = $this->checkOpenTime($startTime, $endTime);
        
        if(!$open)
        {
            Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลราคาสนามได้ กรุณาสร้างข้อมูลในช่วงเวลาที่สนามเปิดให้บริการ');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                ->withInput(Input::except('password'));
        }
        $tmp_day = '';
        $day = array();
        if($request->input('day_0'))
        {
            $tmp_day = 'Sun ';
            array_push($day, 'Sun'); 
        }            
        if($request->input('day_1'))
        {
            $tmp_day .= 'Mon ';
            array_push($day, 'Mon');
        }            
        if($request->input('day_2'))
        {
            $tmp_day .= 'Tue ';
            array_push($day, 'Tue');
        }
        if($request->input('day_3'))
        {
            $tmp_day .= 'Wed ';
            array_push($day, 'Wed');
        }
        if($request->input('day_4'))
        {    
            $tmp_day .= 'Thu ';
            array_push($day, 'Thu');
        }
        if($request->input('day_5'))
        {    
            $tmp_day .= 'Fri ';
            array_push($day, 'Fri');
        }
        if($request->input('day_6'))
        {
            $tmp_day .= 'Sat ';
            array_push($day, 'Sat');
        }        
        if($request->input('holiday'))
        {
            $tmp_day .= 'Holiday';
            array_push($day, 'Holiday');
        }

        if(count($day) <= 0)
        {
            Session::flash('error_msg', 'กรุณาระบุวันที่ต้องการ');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                ->withInput(Input::except('password'));  
        }

        foreach ($day as $d) {
            //$checkOverlap = Tmp_Field_Price::checkOverlap($request->input('field'), $startTime, $endTime, $d )->get();
            $checkOverlap = $this->checkOverlapFieldPrice($request->input('field'), $startTime, $endTime, $d );
            if($checkOverlap > 0)
            {
                Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลราคาสนามได้ มีข้อมูลซ้ำ');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                    ->withInput(Input::except('password'));                
            }
        }
        

        if($checkOverlap == 0)
        {       

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
            {
                Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลราคาสนามได้!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                    ->withErrors($validator)
                    ->withInput(Input::except('password')); 
            }
            else
            {
                $tmp_field_price = new Tmp_Field_Price();
                $tmp_field_price->field_id = $request->input('field');
                $tmp_field_price->price = $request->input('field_price');
                $tmp_field_price->start_time = $request->input('start_time');
                $tmp_field_price->end_time = $request->input('end_time');
                $tmp_field_price->day  = $tmp_day;
                $tmp_field_price->set_color = $request->input('bgColor');
                $tmp_field_price->save();
                $this->editReserveAfterPriceChange();
                Session::flash('success_msg', 'เพิ่มข้อมูลราคาสนามเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price');
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลราคาสนามได้ มีข้อมูลซ้ำ'. $checkOverlap);
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                ->withInput(Input::except('password'));
        }
    }

    public function editFieldPrice(Request $request, $stadium_name)
    {
        $tmp_field_price = Tmp_Field_Price::where('id', $request->input('hdd_field_price'))->first();
        if(count($tmp_field_price) > 0)
        {
            $rules = array(
                'field' => 'required|integer',
                'field_price' => 'required|integer',
                'start_time' => 'required',
                'end_time' => 'required',
                // 'start' => 'required|date_format:Y-m-d',
                // 'end' => 'required|date_format:Y-m-d',
            );
            $startTime = new Datetime($request->input('start_time'));
            $endTime = new Datetime($request->input('end_time'));
            // $startDate = new Datetime($request->input('start'));
            // $endDate = new Datetime($request->input('end'));

            $open = $this->checkOpenTime($startTime, $endTime);
        
            if(!$open)
            {
                Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลราคาสนามได้ กรุณาสร้างข้อมูลในช่วงเวลาที่สนามเปิดให้บริการ');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                    ->withInput(Input::except('password'));
            }

            $tmp_day = '';
            $day = array();
            if($request->input('day_0'))
            {
                $tmp_day = 'Sun ';
                array_push($day, 'Sun'); 
            }            
            if($request->input('day_1'))
            {
                $tmp_day .= 'Mon ';
                array_push($day, 'Mon');
            }            
            if($request->input('day_2'))
            {
                $tmp_day .= 'Tue ';
                array_push($day, 'Tue');
            }
            if($request->input('day_3'))
            {
                $tmp_day .= 'Wed ';
                array_push($day, 'Wed');
            }
            if($request->input('day_4'))
            {    
                $tmp_day .= 'Thu ';
                array_push($day, 'Thu');
            }
            if($request->input('day_5'))
            {    
                $tmp_day .= 'Fri ';
                array_push($day, 'Fri');
            }
            if($request->input('day_6'))
            {
                $tmp_day .= 'Sat ';
                array_push($day, 'Sat');
            }        
            if($request->input('holiday'))
            {
                $tmp_day .= 'Holiday';
                array_push($day, 'Holiday');
            }

            if(count($day) <= 0)
            {
                Session::flash('error_msg', 'กรุณาระบุวันที่ต้องการ');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                    ->withInput(Input::except('password'));  
            }

            foreach ($day as $d) {
                // $checkOverlap = Tmp_Field_Price::checkOverlap($request->input('field'), $startTime, $endTime, $d, $tmp_field_price->id )->get();
                $checkOverlap = $this->checkOverlapFieldPrice($request->input('field'), $startTime, $endTime, $d, $tmp_field_price->id );
                if($checkOverlap > 0)
                {
                    Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลราคาสนามได้ มีข้อมูลซ้ำ');
                    return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                        ->withInput(Input::except('password'));                
                }
            }

            if($checkOverlap == 0)
            {
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) 
                {
                    Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลราคาสนามได้!');
                    return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                        ->withErrors($validator)
                        ->withInput(Input::except('password')); 
                }
                else
                {
                    $tmp_field_price->field_id = $request->input('field');
                    $tmp_field_price->price = $request->input('field_price');
                    $tmp_field_price->start_time = $request->input('start_time');
                    $tmp_field_price->end_time = $request->input('end_time');
                    $tmp_field_price->day  = $tmp_day;
                    $tmp_field_price->set_color = $request->input('bgColor');
                    $tmp_field_price->save();
                    $this->editReserveAfterPriceChange();
                    Session::flash('success_msg', 'แก้ไขข้อมูลราคาสนามเรียบร้อยแล้ว!');
                    return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price');
                }
            }
            else
            {
                Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลราคาสนามได้ มีข้อมูลซ้ำ'. $checkOverlap);
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                    ->withInput(Input::except('password'));
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลราคาสนามได้! ไม่พบข้อมูลราคานี้ในระบบ');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                ->withErrors($validator)
                ->withInput(Input::except('password')); 
        }
        
    }
    
    public function deleteFieldPrice(Request $request, $stadium_name)
    {
        $tmp_field_price = Tmp_Field_Price::where('id', $request->input('del-field_price'))->first();

        if(count($tmp_field_price) > 0)
        {
            $tmp_field_price->delete();
            $this->editReserveAfterPriceChange();
            Session::flash('success_msg', 'ลบข้อมูลราคาเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price');
        }
        else
        {
            Session::flash('error_msg', 'ไม่พบข้อมูลราคาในฐานข้อมูล!');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price')
                ->withInput(Input::except('password'));
        }
    }

    public function editStadium(Request $request, $stadium_name)
    {
        $stadium = Stadium::where('id', Auth::user()->stadium_id)->first();

        if(count($stadium) > 0)
        {
            $rules = array(
                'staduim_name_edit' => 'max:50|unique:stadium,name,'.$stadium->id,
                'address' => 'max:200',
                'detail' => 'max:200',
                'openTime' => 'required',
                'closeTime' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
            {
                Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลสนามได้!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_stadium')
                    ->withErrors($validator)
                    ->withInput(Input::except('password'));                
            }
            else
            {
                $stadium->name = $request->input('staduim_name_edit');
                $stadium->open_time = $request->input('openTime');
                $stadium->close_time = $request->input('closeTime');
                $stadium->address = $request->input('address');
                $stadium->detail = $request->input('detail');
                $stadium->save();
                Session::flash('success_msg', 'แก้ไขข้อมูลสนามเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_stadium');
            }            
        }
    }

    function checkOverlapHoliday($startTime, $endTime, $holidayId = 0)
    {
        $holidays = Holidays::where('stadium_id', '=', Auth::user()->stadium_id)
                                    ->where('id', '!=', $holidayId)->get();
        $result = 0;                    
        foreach ($holidays as $holiday) {

            $start_Time = new Datetime($holiday->start_time);
            $end_Time = new Datetime($holiday->end_time);
            $startDate = new Datetime($holiday->start_date);
            $endDate = new Datetime($holiday->end_date);

            $start_time = new DateTime($startDate->format('Y-m-d')  .' ' .$start_Time->format('H:i:s'));
            $end_time = new DateTime($endDate->format('Y-m-d')  .' ' .$end_Time->format('H:i:s'));

            // Log::info('fieldId: ' . $fieldId .', fieldpriceId: ' . $fieldpriceId);
            Log::info('$start_time: ' . date_format($start_time, 'Y-m-d H:i:s' ) .', $end_time: '. date_format($end_time, 'Y-m-d H:i:s' ) .', $startTime: '. date_format($startTime, 'Y-m-d H:i:s' ) .', $endTime: ' . date_format($endTime, 'Y-m-d H:i:s' ));
            if($start_time > $end_time)
                $end_time->modify('+1 day');
            
            if($startTime > $endTime)
                $endTime->modify('+1 day');
            Log::info('$start_time: ' . date_format($start_time, 'Y-m-d H:i:s' ) .', $end_time: '. date_format($end_time, 'Y-m-d H:i:s' ) .', $startTime: '. date_format($startTime, 'Y-m-d H:i:s' ) .', $endTime: ' . date_format($endTime, 'Y-m-d H:i:s' ));
            if(($start_time < $endTime) && ($end_time > $startTime))
                $result++;
        }
        return $result;        
        
    }

    function checkOverlapFieldPrice($fieldId, $startTime, $endTime, $day, $fieldpriceId = 0)
    {
        $fieldPrices = Tmp_Field_Price::where('field_id', '=', $fieldId)
                                    ->where('tmp_field_price.id', '!=', $fieldpriceId)      
                                    ->where('tmp_field_price.day', 'like', '%' . $day . '%')->get();
        $result = 0;                    
        foreach ($fieldPrices as $fieldPrice) {
            $start_time = new DateTime($fieldPrice->start_time);
            $end_time = new DateTime($fieldPrice->end_time);

            Log::info('fieldId: ' . $fieldId .', fieldpriceId: ' . $fieldpriceId);
            Log::info('$start_time: ' . date_format($start_time, 'Y-m-d H:i:s' ) .', $end_time: '. date_format($end_time, 'Y-m-d H:i:s' ) .', $startTime: '. date_format($startTime, 'Y-m-d H:i:s' ) .', $endTime: ' . date_format($endTime, 'Y-m-d H:i:s' ));
            if($start_time > $end_time)
                $end_time->modify('+1 day');
            
            if($startTime > $endTime)
                $endTime->modify('+1 day');
            Log::info('$start_time: ' . date_format($start_time, 'Y-m-d H:i:s' ) .', $end_time: '. date_format($end_time, 'Y-m-d H:i:s' ) .', $startTime: '. date_format($startTime, 'Y-m-d H:i:s' ) .', $endTime: ' . date_format($endTime, 'Y-m-d H:i:s' ));
            if(($start_time < $endTime) && ($end_time > $startTime))
                $result++;
        }
        return $result;        
        
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

    public function addPromotion(Request $request, $stadium_name)
    {
        $rules = array(
            'promotion_name' => 'required',
            'discount' => 'required|integer',
            'discount_type' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d',
        );
        $startTime = new Datetime($request->input('start_time'));
        $endTime = new Datetime($request->input('end_time'));
        $startDate = new Datetime($request->input('start'));
        $endDate = new Datetime($request->input('end'));

        $open = $this->checkOpenTime($startTime, $endTime);
        
        if(!$open)
        {
            Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลราคาโปรโมรชั่นได้ กรุณาสร้างข้อมูลในช่วงเวลาที่สนามเปิดให้บริการ');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion')
                ->withInput(Input::except('password'));
        }

        $checkOverlap = Promotions::checkOverlap(Auth::user()->stadium_id, $startTime, $endTime, $startDate, $endDate )->get();

        if(count($checkOverlap) == 0)
        {

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
            {
                Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลโปรโมรชั่นได้');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion')
                    ->withErrors($validator)
                    ->withInput(Input::except('password')); 
            }
            else
            {
                $promotion = new Promotions();
                $promotion->stadium_id = Auth::user()->stadium_id;
                $promotion->name = $request->input('promotion_name');
                $promotion->start_time = $request->input('start_time');
                $promotion->end_time = $request->input('end_time');
                $promotion->start_date = $request->input('start');
                $promotion->end_date = $request->input('end');
                $promotion->discount = $request->input('discount');
                $promotion->discount_type = $request->input('discount_type');
                $promotion->fixed_range = $request->input('fixed_range') == 'on' ? 1 : 0;
                $promotion->save();
                $this->editReserve();
                Session::flash('success_msg', 'เพิ่มข้อมูลราคาโปรโมรชั่นเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion');
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลราคาโปรโมรชั่นได้ มีข้อมูลซ้ำ'. $checkOverlap);
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion')
                ->withInput(Input::except('password'));
        }
    }

    public function editPromotion(Request $request, $stadium_name)
    {
        $promotion = Promotions::where('id', $request->input('hddpromotion'))->first();

        if(count($promotion) > 0)
        {
            $rules = array(
                'promotion_name' => 'required',
                'discount' => 'required|integer',
                'discount_type' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'start' => 'required|date_format:Y-m-d',
                'end' => 'required|date_format:Y-m-d',
            );
            $startTime = new Datetime($request->input('start_time'));
            $endTime = new Datetime($request->input('end_time'));
            $startDate = new Datetime($request->input('start'));
            $endDate = new Datetime($request->input('end'));
            
            $open = $this->checkOpenTime($startTime, $endTime);
        
            if(!$open)
            {
                Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลโปรโมรชั่นได้ กรุณาสร้างข้อมูลในช่วงเวลาที่สนามเปิดให้บริการ');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion')
                    ->withInput(Input::except('password'));
            }
            $checkOverlap = Promotions::checkOverlap(Auth::user()->stadium_id, $startTime, $endTime, $startDate, $endDate ,$promotion->id)->get();

            if(count($checkOverlap) == 0)
            {
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) 
                {
                    Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลโปรโมรชั่นได้');
                    return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion')
                        ->withErrors($validator)
                        ->withInput(Input::except('password')); 
                }
                else
                {
                    $promotion->name = $request->input('promotion_name');
                    $promotion->start_time = $request->input('start_time');
                    $promotion->end_time = $request->input('end_time');
                    $promotion->start_date = $request->input('start');
                    $promotion->end_date = $request->input('end');
                    $promotion->discount = $request->input('discount');
                    $promotion->discount_type = $request->input('discount_type');
                    $promotion->fixed_range = $request->input('fixed_range') == 'on' ? 1 : 0;
                    $promotion->save();
                    $this->editReserve();
                    Session::flash('success_msg', 'แก้ไขข้อมูลราคาโปรโมรชั่นเรียบร้อยแล้ว!');
                    return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion');
                }
            }
            else
            {
                Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลราคาโปรโมรชั่นได้ มีข้อมูลซ้ำ'. $checkOverlap);
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion')
                    ->withInput(Input::except('password'));
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลโปรโมรชั่นได้ ไม่พบข้อมูลราคานี้ในระบบ');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion')
                ->withInput(Input::except('password')); 
        }
    }

    public function deletePromotion(Request $request, $stadium_name)
    {
        $promotion = Promotions::where('id', $request->input('del-promotion'))->first();

        if(count($promotion) > 0)
        {
            $promotion->delete();
            $this->editReserve();
            Session::flash('success_msg', 'ลบข้อมูลโปรโมรชั่นเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion');
        }
        else
        {
            Session::flash('error_msg', 'ไม่พบข้อมูลโปรโมรชั่นในฐานข้อมูล!');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion')
                ->withInput(Input::except('password'));
        }
    }

    public function addHoliday(Request $request, $stadium_name)
    {
        $rules = array(
            'holiday_name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d',
        );
        
        // $startDate = new Datetime($request->input('start'));
        // $endDate = new Datetime($request->input('end'));       


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) 
        {
            Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลวันหยุดได้');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_holiday')
                ->withErrors($validator)
                ->withInput(Input::except('password')); 
        }
        else
        {
            $startTime = new Datetime($request->input('start_time'));
            $endTime = new Datetime($request->input('end_time'));
            $startDate = new Datetime($request->input('start'));
            $endDate = new Datetime($request->input('end'));
            $start = new Datetime($startDate->format('Y-m-d') . ' ' . $startTime->format('H:i:s'));
            $end = new Datetime($endDate->format('Y-m-d') . ' ' . $endTime->format('H:i:s'));

            $checkOverlap = $this->checkOverlapHoliday($start, $end);

            if($checkOverlap == 0)
            {
                $holidays = new Holidays();
                $holidays->stadium_id = Auth::user()->stadium_id;
                $holidays->name = $request->input('holiday_name');
                $holidays->start_time = $request->input('start_time');
                $holidays->end_time = $request->input('end_time');
                $holidays->start_date = $request->input('start');
                $holidays->end_date = $request->input('end');            
                $holidays->avalible = $request->input('holiday_avalible') == 'on' ? 1 : 0;
                $holidays->save();
                Session::flash('success_msg', 'เพิ่มข้อมูลวันหยุดเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_holiday');
            }
            else
            {
                Session::flash('error_msg', 'ไม่สามารถเพิ่มข้อมูลวันหยุดได้ มีข้อมูลซ้ำ');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_holiday')
                    ->withInput(Input::except('password'));
            }
        }

    }

    public function editHoliday(Request $request, $stadium_name)
    {
        $holidays = Holidays::where('id', $request->input('hddholiday'))->first();

        if(count($holidays) > 0)
        {
            $rules = array(
                'holiday_name' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'start' => 'required|date_format:Y-m-d',
                'end' => 'required|date_format:Y-m-d',
            );
            
            // $startDate = new Datetime($request->input('start'));
            // $endDate = new Datetime($request->input('end'));       


            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
            {
                Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลวันหยุดได้');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_holiday')
                    ->withErrors($validator)
                    ->withInput(Input::except('password')); 
            }
            else
            {
                $startTime = new Datetime($request->input('start_time'));
                $endTime = new Datetime($request->input('end_time'));
                $startDate = new Datetime($request->input('start'));
                $endDate = new Datetime($request->input('end'));
                $start = new Datetime($startDate->format('Y-m-d') . ' ' . $startTime->format('H:i:s'));
                $end = new Datetime($endDate->format('Y-m-d') . ' ' . $endTime->format('H:i:s'));
                $checkOverlap = $this->checkOverlapHoliday($start, $end, $holidays->id);

                if($checkOverlap == 0)
                {
                    $holidays->name = $request->input('holiday_name');
                    $holidays->start_time = $request->input('start_time');
                    $holidays->end_time = $request->input('end_time');
                    $holidays->start_date = $request->input('start');
                    $holidays->end_date = $request->input('end');            
                    $holidays->avalible = $request->input('holiday_avalible') == 'on' ? 1 : 0;
                    $holidays->save();
                    Session::flash('success_msg', 'แก้ไขข้อมูลวันหยุดเรียบร้อยแล้ว!');
                    return Redirect::to('/'. $stadium_name .'/dashboard#panel1_holiday');
                }
                else
                {
                    Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลวันหยุดได้ มีข้อมูลซ้ำ');
                    return Redirect::to('/'. $stadium_name .'/dashboard#panel1_holiday')
                        ->withInput(Input::except('password'));
                }
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลวันหยุดได้ ไม่พบข้อมูลนี้ในระบบ');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_holiday')
                ->withInput(Input::except('password')); 
        }

    }

    public function deleteHoliday(Request $request, $stadium_name)
    {
        $holidays = Holidays::where('id', $request->input('del-holiday'))->first();

        if(count($holidays) > 0)
        {
            $holidays->delete();
            Session::flash('success_msg', 'ลบข้อมูลวันหยุดเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_holiday');
        }
        else
        {
            Session::flash('error_msg', 'ไม่พบข้อมูลวันหยุดในฐานข้อมูล!');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_holiday')
                ->withInput(Input::except('password'));
        }
    }

    public function editReserve()
    {
        $reservations = Reservation::where('status','=', 1)->get();            
        
        foreach ($reservations as $reservation)
        {
            $reserveStartDate = date('Y-m-d', strtotime($reservation->start_time));
            $reserveEndDate = date('Y-m-d', strtotime($reservation->end_time));
            $reserveStarttime = date('H:i:s', strtotime($reservation->start_time));
            $reserveEndtime = date('H:i:s', strtotime($reservation->end_time));

            $totalDiscount = $this->promotion(new Datetime($reserveStarttime), new Datetime($reserveEndtime), new Datetime($reserveStartDate), new Datetime($reserveEndDate), $reservation->field_price);
            if($totalDiscount >= 0)
            {
                $reservation->discount_price = $totalDiscount;
                $reservation->save();
            }
            Log::info('$reservation:' . $reservation->id . ', $totalDiscount: '.$totalDiscount .', $reservation:' . $reservation->field_price);
        }
    }

    public function promotion($reserveStarttime, $reserveEndtime, $reserveStartDate, $reserveEndDate, $totalPrice)
    {
        $promotions = Promotions::where('stadium_id', Auth::user()->stadium_id)->get();
        $totalDiscount = 0;
        $over_flag_promo = 0;
        $done_flag_promo = 0;
        $minutes_to_add = 1;
        $discount = 0;
        $discountType = '';
        
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
                // Log::info('$tmpStart1: '. date_format($tmpStart1,'Y-m-d H:i:s' ) .'$tmpStart2: '. date_format($tmpStart2,'Y-m-d H:i:s' ) . '$tmpEnd1: ' . date_format($tmpEnd1,'Y-m-d H:i:s' )  . '$tmpEnd2: ' . date_format($tmpEnd2,'Y-m-d H:i:s' ) );                 
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
                    // Log::info('pass 2st if $startTime: ' . date_format($startTime,'Y-m-d H:i:s' )  . ', $endTime: '. date_format($endTime,'Y-m-d H:i:s' ) );
                    if($endTime > $startTime)
                        $totalDiscount += $this->discounting($startTime, $endTime, $discount, $totalPrice, $discountType, $reserveStarttime, $reserveEndtime);                                
                     
                    
                }                
                else if($done_flag_promo == 0 && $over_flag_promo == 1 && ($tmpStarttime >= $tmpStart2 && $tmpStarttime < $tmpEnd2))
                {
                    // Log::info('pass 3st if');  
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
                    // Log::info('pass 2st if $startTime: ' . date_format($startTime,'Y-m-d H:i:s' )  . ', $endTime: '. date_format($endTime,'Y-m-d H:i:s' ) );
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
                    //Log::info('foreach2 $tmpStart1: '. date_format($tmpStart1,'Y-m-d H:i:s' ) .'$tmpStart2: '. date_format($tmpStart2,'Y-m-d H:i:s' )  . '$tmpEnd2: ' . date_format($tmpEnd2,'Y-m-d H:i:s' ) );                    
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
                        //Log::info('pass 2st if $startTime: ' . date_format($startTime,'Y-m-d H:i:s' )  . ', $endTime: '. date_format($endTime,'Y-m-d H:i:s' ) );
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

    public function editReserveAfterPriceChange()
    {

        $reservations = Reservation::where('status','=', 1)->get();            
        
        foreach ($reservations as $reservation)
        {
            $tmpReserveStartDate = date('Y-m-d', strtotime($reservation->start_time));
            $tmpReserveEndDate = date('Y-m-d', strtotime($reservation->end_time));
            $tmpReserveStarttime = date('H:i:s', strtotime($reservation->start_time));
            $tmpReserveEndtime = date('H:i:s', strtotime($reservation->end_time));
            
            $tmp_holiday = Holidays::where('stadium_id', Auth::user()->stadium_id)->where('avalible',1)->where('start_date', '<=', $tmpReserveEndDate)->where('end_date', '>=', $tmpReserveStartDate)->get();

            $startDay = date('D', strtotime($reservation->start_time));
            $endDay = date('D', strtotime($reservation->end_time));
            
            if(count($tmp_holiday) > 0)
                $tmp_field_price = Tmp_Field_Price::where('field_id', $reservation->field_id)->where('tmp_field_price.day', 'like', '%Holiday%')->orderBy('start_time', 'asc')->get();
            elseif($startDay != $endDay)
                $tmp_field_price = Tmp_Field_Price::where('field_id', $reservation->field_id)->where('tmp_field_price.day', 'like', '%' . $startDay . '%')->orWhere('tmp_field_price.day', 'like', '%' . $endDay . '%')->orderBy('start_time', 'asc')->get();
            else
                $tmp_field_price = Tmp_Field_Price::where('field_id', $reservation->field_id)->where('tmp_field_price.day', 'like', '%' . $startDay . '%')->orderBy('start_time', 'asc')->get();
            //$tmp_field_price = Tmp_Field_Price::where('field_id', $request->input('hddResourceId'))->orderBy('start_time', 'asc')->get();
            
            //$reservationDay = Reservation::where('start_time', $request->input('hddDate'))->get();

            // $open = $this->checkOpenTime($reserveStarttime, $reserveEndtime);
    
            // if(!$open)
            // {
            //     Session::flash('error_msg', 'ไม่สามารถแก้ไขการจองได้ กรุณาจองในช่วงเวลาที่สนามเปิดให้บริการ');
            //     return Redirect::to('/'. $stadium .'/reservation')
            //         ->withInput(Input::except('password'));
            // }

            // $start1 = new DateTime($reserveStartDate->format('Y-m-d') .' ' .$reserveStarttime->format('H:i:s'));
            // $end1 = new DateTime($reserveEndDate->format('Y-m-d') .' ' .$reserveEndtime->format('H:i:s'));
            // $checkOverlap = Reservation::checkOverlap($request->input('hddResourceId'), $start1, $end1, $reservation->id)->get();

                            
            $over_flag = 0;
            $left_period_flag = 0;
            $done_flag = 0;
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

                $reserveStarttime = new Datetime($tmpReserveStarttime);
                if($left_period_flag == 0)
                    $reserveEndtime = new Datetime($tmpReserveEndtime);
                else
                    $reserveEndtime = $tmpEndtime;
                $reserveStartDate = new Datetime($tmpReserveStartDate);
                $reserveEndDate = new Datetime($tmpReserveEndDate);
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
                            // $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                        }                                                         
                        
                    }
                    else if($done_flag == 0 && $over_flag == 1 && ($tmpStarttime >= $tmpStart2 && $tmpStarttime < $tmpEnd2))
                    {
                        
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
                            // $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
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
                        $reserveEndtime = new Datetime($tmpReserveEndtime);
                    else
                        $reserveEndtime = $tmpEndtime;
                    
                    $reserveStartDate = new Datetime($tmpReserveStartDate);
                    $reserveEndDate = new Datetime($tmpReserveEndDate);
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
                                // $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                            }                      
                            
                        }                
                        else if($done_flag == 0 && $over_flag == 1 && ($tmpStarttime >= $tmpStart3 && $tmpStarttime < $tmpEnd3))
                        {
                
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
                                // $totalDiscount += $this->promotion($startTime, $endTime, $reserveStartDate, $reserveEndDate, $stadium_data, $thisPrice);
                            }  
                        }
                    } 
                                    
                }
            }
            
            // if($totalPrice > 0 && $done_flag == 1)
            //     $this->editReservation($reservation, $totalDiscount, $start1, $end1, $request, $totalPrice);
            Log::info('$reservation:' . $reservation->id . ', $totalPrice: '.$totalPrice .', $old_totalPrice:' . $reservation->field_price); 
            if($totalPrice >= 0)
            {
                $reservation->field_price = $totalPrice;
                $reservation->save();
            }
            
        }
        
        
    }

    public function calTotalPrice($startTime, $endTime, $minCost)
    {
        $time = $startTime->diff($endTime)->format('%H:%i:%s');
        $arrTime = explode(":", $time);
        $totalPrice = (($arrTime[0] * 60) + $arrTime[1]) * $minCost;

        return $totalPrice;
    }

}
