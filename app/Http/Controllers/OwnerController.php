<?php

namespace App\Http\Controllers;

use Validator;
use Session;
use Hash;
use Auth;
use App\User as Users;
use App\Role as Role;
use App\Stadium as Stadium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class OwnerController extends Controller
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

    public function show($stadium)
    {
        $owner_users = Stadium::owner()->get();
        $stadiums = Stadium::all();
        $roles = Role::all();
        return view('pages.owner_management', compact('stadiums', 'stadium', 'owner_users', 'roles'));
    }

    public function addOwner(Request $request, $stadium)
    {
         $rules = array(
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:users',
            'password' => 'required|min:6',
            'email' => 'required|email|max:255|unique:users',            
            'stadium_id' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) 
        {
            Session::flash('error_msg', 'ไม่สามารถเพิ่มบัญชีผู้ใช้ได้!');
        	return Redirect::to('/' . $stadium . '/owner_management')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
        else
        {
            $name = explode(" ", Input::get('name'));
            $user = New Users();
            $user->firstname	= $name[0];
            $user->lastname   	= $name[1];
            $user->username  	= Input::get('username');
            $user->password     = Hash::make(Input::get('password'));
            $user->email      	= Input::get('email');
            $user->role_id      = '3';
            $user->stadium_id   = Input::get('stadium_id');
            $user->save();
            Session::flash('success_msg', 'เพิ่มบัญชีผู้ใช้เรียบร้อยแล้ว!');
            return Redirect::to('/' . $stadium . '/owner_management');         
        }
    }

    public function updateOwner(Request $request, $stadium, $username)
    {
        $user = Users::where('username', $username) -> first();
        $rules = array(
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:users,username,'.$user->id,
            'password' => 'min:6',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,          
            'stadium_id' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) 
        {
            Session::flash('error_msg', 'ไม่สามารถแก้ไขบัญชีผู้ใช้ได้!');
        	return Redirect::to('/'. $stadium .'/owner_management')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
        else
        {
            if(Input::has('password'))
            {
                $user->password = Hash::make(Input::get('password'));
            }
            $name = explode(" ", Input::get('name'));         
            if(count($name) > 1)
            {
                $user->firstname	= $name[0];
                $user->lastname   	= $name[1];
            }
            else
                $user->firstname    = Input::get('name');
            $user->username  	= Input::get('username');
            $user->email      	= Input::get('email');
            $user->role_id      = '3';
            $user->stadium_id   = Input::get('stadium_id');
            $user->save();
            Session::flash('success_msg', 'แก้ไขบัญชีผู้ใช้เรียบร้อยแล้ว!');
            return Redirect::to('/'. $stadium .'/owner_management');
        }
    }

    public function deleteOwner(Request $request, $stadium)
    {
        $validator = Validator::make(Input::all(), array('del-user' => 'required'));
        if (!$validator->fails())
        {

            $username = $request->input('del-user');
            if($username != Auth::user()->username)
            {
                $user = Users::where('username', $username) -> first();
                $stadium_users = Stadium::where('id', Auth::user()->stadium_id)->first();

                if($stadium == $stadium_users->name)
                {
                    $user->delete();
                    Session::flash('success_msg', 'ลบบัญชีผู้ใช้เรียบร้อยแล้ว!');
                    return Redirect::to('/'. $stadium .'/owner_management');
                }
                else
                {
                    Session::flash('error_msg', 'ไม่สามารถลบบัญชีผู้ใช้ได้!');
                    return Redirect::to('/'. $stadium .'/owner_management');
                }
            }
            else
            {
                Session::flash('error_msg', 'คุณไม่สามารถลบบัญชีของตนเองได้!');
                return Redirect::to('/'. $stadium .'/owner_management');
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถลบบัญชีผู้ใช้ได้!');
            return Redirect::to('/'. $stadium .'/owner_management');
        }
    }
}
