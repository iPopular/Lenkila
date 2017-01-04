<?php

namespace App\Http\Controllers;

use Auth;
use App\Field as Field;
use Validator;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ReservationController extends Controller
{

    public function show(Request $request, $stadium)
    {
        $fields = Field::where('stadium_id', Auth::user()->stadium_id)->get();
        $resource = array();
        $i = 0;
        foreach($fields as $field)
        {
            $resource[$i]['id'] = $field['id'];
            $resource[$i]['title'] = $field['name'];
            $i++;
        }
        return view('pages.reservation', compact('stadium', 'resource'));
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
            $field->stadium_id = Auth::user()->stadium_id;
            $field->created_by = Auth::user()->id;
            $field->save();
            Session::flash('success_msg', 'เพิ่มสนามเรียบร้อยแล้ว!');
            return Redirect::to('/'. $stadium .'/reservation');
        }
    }
}
