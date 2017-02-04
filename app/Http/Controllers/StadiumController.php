<?php

namespace App\Http\Controllers;

use Validator;
use Session;
use Hash;
use Auth;
use Illuminate\Http\Request;
use App\Stadium as Stadium;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class StadiumController extends Controller
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

    public function show($stadium_name)
    {
        $stadiums = Stadium::all();
        return view('pages.stadium_management', compact('stadiums', 'stadium_name'));
    }

    public function addStadium(Request $request, $stadium_name)
    {
         $rules = array(
            'name' => 'required|max:50|unique:stadium',
            'detail' => 'required|max:200',
            'address' => 'required|max:200',
            'open_time' => 'required',
            'close_time' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) 
        {
            Session::flash('error_msg', 'ไม่สามารถเพิ่มสนามได้!');
        	return Redirect::to('/' . $stadium_name . '/stadium_management')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
        else
        {
            $stadium = New Stadium();
            $stadium->name = $request->input('name');
            $stadium->open_time = $request->input('open_time');
            $stadium->close_time = $request->input('close_time');
            $stadium->address = $request->input('address');
            $stadium->detail = $request->input('detail');
            $stadium->save();
            Session::flash('success_msg', 'เพิ่มสนามเรียบร้อยแล้ว!');
            return Redirect::to('/' . $stadium_name . '/stadium_management');         
        }
    }

    public function updateStadium(Request $request, $stadium_name)
    {
        $stadium = Stadium::where('id' , $request->input('hddStadiumId'))->first();
        if(count($stadium))
        {
            $rules = array(
                'name' => 'required|max:50|unique:stadium,name,'.$stadium->id,
                'detail' => 'required|max:200',
                'address' => 'required|max:200',
                'open_time' => 'required',
                'close_time' => 'required',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) 
            {
                Session::flash('error_msg', 'ไม่สามารถแก้ไขสนามได้!');
                return Redirect::to('/' . $stadium_name . '/stadium_management')
                    ->withErrors($validator)
                    ->withInput(Input::except('password'));
            }
            else
            {
                $stadium->name = $request->input('name');
                $stadium->open_time = $request->input('open_time');
                $stadium->close_time = $request->input('close_time');
                $stadium->address = $request->input('address');
                $stadium->detail = $request->input('detail');
                $stadium->save();
                Session::flash('success_msg', 'แก้ไขสนามเรียบร้อยแล้ว!');
                return Redirect::to('/' . $stadium_name . '/stadium_management');         
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่พบข้อมูลสนามในฐานข้อมูลได้!');
            return Redirect::to('/' . $stadium_name . '/stadium_management')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }      
    }

    public function deleteStadium(Request $request, $stadium_name)
    {
        $validator = Validator::make(Input::all(), array('del-stadium' => 'required'));
        if (!$validator->fails())
        {

            $stadium = Stadium::where('id', $request->input('del-stadium')) -> first();
            if(count($stadium))
            {
                $stadium->delete();
                Session::flash('success_msg', 'ลบบัญชีผู้ใช้เรียบร้อยแล้ว!');
                return Redirect::to('/'. $stadium_name .'/stadium_management');
            }
            else
            {
                Session::flash('error_msg', 'ไม่พบข้อมูลสนามในฐานข้อมูลได้!');
                return Redirect::to('/' . $stadium_name . '/stadium_management')
                    ->withErrors($validator)
                    ->withInput(Input::except('password'));
            }

        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถลบบัญชีผู้ใช้ได้!');
            return Redirect::to('/'. $stadium_name .'/stadium_management')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
    }
}
