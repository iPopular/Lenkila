<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Session;
use App\Stadium as Stadium;
use App\Tmp_Field_Price as Tmp_Field_Price;
use App\Promotions as Promotions;
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
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d',
        );

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
            $tmp_field_price->end_time = $request->input('end_time');//
            $tmp_field_price->start_date = $request->input('start');
            $tmp_field_price->end_date = $request->input('end');
            $tmp_field_price->set_color = $request->input('bgColor');
            $tmp_field_price->save();
            Session::flash('success_msg', 'เพิ่มข้อมูลราคาสนามเรียบร้อยแล้ว!');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price');
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
                'start' => 'required|date_format:Y-m-d',
                'end' => 'required|date_format:Y-m-d',
            );

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
                $tmp_field_price->end_time = $request->input('end_time');//
                $tmp_field_price->start_date = $request->input('start');
                $tmp_field_price->end_date = $request->input('end');
                $tmp_field_price->set_color = $request->input('bgColor');
                $tmp_field_price->save();
                Session::flash('success_msg', 'เพิ่มข้อมูลราคาสนามเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_field_price');
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
            Session::flash('success_msg', 'เพิ่มข้อมูลราคาโปรโมรชั่นเรียบร้อยแล้ว!');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion');
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
                Session::flash('success_msg', 'แก้ไขข้อมูลราคาโปรโมรชั่นเรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion');
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถแก้ไขข้อมูลโปรโมรชั่นได้! ไม่พบข้อมูลราคานี้ในระบบ');
            return Redirect::to('/'. $stadium_name .'/dashboard#panel1_promotion')
                ->withErrors($validator)
                ->withInput(Input::except('password')); 
        }
    }

    public function deletePromotion(Request $request, $stadium_name)
    {
        $promotion = Promotions::where('id', $request->input('del-promotion'))->first();

        if(count($promotion) > 0)
        {
            $promotion->delete();
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

}
