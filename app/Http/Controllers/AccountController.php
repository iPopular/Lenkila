<?php

namespace App\Http\Controllers;

use Validator;
use Session;
use Hash;
use Auth;
use Illuminate\Http\Request;
use App\User as Users;
use App\Stadium as Stadium;
use App\Role as Role;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;


class AccountController extends Controller
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

    public function show(Request $request, $stadium)
    {
        $stadium_users = Stadium::find(Auth::user()->stadium_id)->first();
                        
        $roles = Role::all();

        return view('pages.account_management', compact('roles', 'stadium', 'stadium_users'));

        
    }

    public function addAccount(Request $request, $stadium)
    {
        $rules = array(
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'username' => 'required|max:255|unique:users',
            'password' => 'required|min:6',
            'email' => 'required|email|max:255|unique:users',            
            'role_id' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) 
        {
            Session::flash('error_msg', 'ไม่สามารถเพิ่มบัญชีผู้ใช้ได้!');
        	return Redirect::to('/' . $stadium . '/account_management')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
        else
        {
            $user = New Users();
            $user->firstname	= Input::get('firstname');
	        $user->lastname   	= Input::get('lastname');
	        $user->username  	= Input::get('username');
            $user->password     = Hash::make(Input::get('password'));
	        $user->email      	= Input::get('email');
            $user->role_id      = Input::get('role_id');
            $user->stadium_id   = Auth::user()->stadium_id;
            $user->save();
            Session::flash('success_msg', 'เพิ่มบัญชีผู้ใช้เรียบร้อยแล้ว!');
	        return Redirect::to('/' . $stadium . '/account_management');
        }
    }

    public function updateAccount(Request $request, $stadium, $username)
    {
        $user = Users::where('username', $username) -> first();
        $rules = array(
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'username' => 'required|max:255|unique:users,username,'.$user->id,
            'password' => 'min:6',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,          
            'role_id' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) 
        {
            Session::flash('error_msg', 'ไม่สามารถแก้ไขบัญชีผู้ใช้ได้!');
        	return Redirect::to('/'. $stadium .'/account_management')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
        else
        {
            if(Input::has('password'))
	        {
	            $user->password = Hash::make(Input::get('password'));
	        }             
            $user->firstname	= Input::get('firstname');
	        $user->lastname   	= Input::get('lastname');
	        $user->username  	= Input::get('username');
	        $user->email      	= Input::get('email');
            $user->role_id      = Input::get('role_id');
            $user->stadium_id   = Auth::user()->stadium_id;
            $user->save();
            Session::flash('success_msg', 'แก้ไขบัญชีผู้ใช้เรียบร้อยแล้ว!');
	        return Redirect::to('/'. $stadium .'/account_management');
        }
    }

    public function deleteAccount(Request $request, $stadium)
    {
        $validator = Validator::make(Input::all(), array('del-user' => 'required'));
        if (!$validator->fails())
        {
            $username = $request->input('del-user');
            if($username != Auth::user()->username)
            {
                $user = Users::where('username', $username) -> first();
                $stadium_users = Stadium::find(Auth::user()->stadium_id)->first();

                if($stadium == $stadium_users->name)
                {
                    $user->delete();
                    Session::flash('success_msg', 'ลบบัญชีผู้ใช้เรียบร้อยแล้ว!');
                    return Redirect::to('/'. $stadium .'/account_management');
                }
                else
                {
                    Session::flash('error_msg', 'ไม่สามารถลบบัญชีผู้ใช้ได้!');
                    return Redirect::to('/'. $stadium .'/account_management');
                }
            }
            else
            {
                Session::flash('error_msg', 'คุณไม่สามารถลบบัญชีของตนเองได้!');
                return Redirect::to('/'. $stadium .'/account_management');
            }
        }
        else
        {
            Session::flash('error_msg', 'ไม่สามารถลบบัญชีผู้ใช้ได้!');
            return Redirect::to('/'. $stadium .'/account_management');
        }
    }
}
