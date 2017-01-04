<?php

namespace App\Http\Controllers;

use Auth;
use App\Field as Field;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

    public function show()
    {
        $fields = Field::where('stadium_id', Auth::user()->stadium_id)->first();
        $resource = array();
        if(count($fields) > 0)
        {
            $i = 0;
            foreach ($fields as $field)
            {
                $resource[$i]['id'] = $field['id'];
                $resource[$i]['title'] = $field['name'];
                $i++;
            }
        }
        
        return view('pages.reservation', compact('resource'));
    }    
}
