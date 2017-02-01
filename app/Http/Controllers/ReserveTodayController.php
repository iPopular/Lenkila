<?php

namespace App\Http\Controllers;

use auth;
use Validator;
use Session;
use App\Stadium as Stadium;
use App\Reservation as Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class ReserveTodayController extends Controller
{
    public function show(Request $request, $stadium)
    {
        $reservation = Stadium::where('id', Auth::user()->stadium_id)->first();

        $events = array();
        $j = 0;

        foreach($reservation->field as $field)
        {
            foreach($field->reservation as $reserv)
            {
                if($reserv->start_time >= date("Y-m-d 00:00:00") && $reserv->end_time <= date("Y-m-d 23:59:59"))
                {
                    $events[$j]['id'] = $reserv->id;
                    $events[$j]['field_price'] = $reserv->field_price;
                    $events[$j]['water_price'] = $reserv->water_price;
                    $events[$j]['supplement_price'] = $reserv->supplement_price;
                    $events[$j]['discount_price'] = $reserv->discount_price;
                    $events[$j]['nickname'] = $reserv->customer->nickname;
                    $events[$j]['mobile_number'] = $reserv->customer->mobile_number;
                    $events[$j]['field_name'] = $reserv->field->name;
                    $events[$j]['start_time'] = $reserv->start_time;
                    $events[$j]['end_time'] = $reserv->end_time;
                    $events[$j]['ref_code'] = $reserv->ref_code;
                    $events[$j]['status'] = $reserv->status;
                    $j++;
                }
            }
        }

        return view('pages.reserve_today', compact('stadium', 'events'));
    }

    public function paidReserve(Request $request, $stadium)
    {
        $reserveId = explode("-", $request->input('hddReserveId'));
        $water_price = $request->input('water_price');
        $supplement_price = $request->input('supplement_price');
        
        foreach($reserveId as $reserve)
        {
            $reservation = Reservation::where('id', $reserve)->first();

            if(count($reservation) > 0)
            {
                try
                {
                    $reservation->status = 2;
                    //$reservation->field_price = $request->input('field_price');
                    $reservation->water_price = $water_price;
                    $reservation->supplement_price = $supplement_price;                    
                    $reservation->save();

                    $water_price = 0;
                    $supplement_price = 0;

                }
                catch(Exception $e)
                {
                    Session::flash('error_msg', 'ไม่สามารถบันทึกได้ พบข้อผิดพลาด' . $e->getMessage());
                    
                }
            }
        
        }
        return Redirect::to('/'. $stadium .'/today')
            ->withInput(Input::except('password'));        
    }
}
