<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;


use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;



class User extends Model implements AuthenticatableContract
{
	use Authenticatable;
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 protected $fillable = [
        'firstname', 'lastname', 'username', 'email', 'password', 'user_role_id',
    ];

	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function stadium()
    {
        return $this->belongsTo('App\Stadium');
    }

    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function hasRole($roles)
	{
		$this->have_role = $this->getUserRole();
		// Check if the user is a Owner account
		if($this->have_role->name == 'root') {
			return true;
		}
		if(is_array($roles)){
			foreach($roles as $need_role){
				if($this->checkIfUserHasRole($need_role)) {
					return true;
				}
			}
		} else{
			return $this->checkIfUserHasRole($roles);
		}
		return false;
	}

	private function getUserRole()
	{
		return $this->role()->getResults();
	}

	private function checkIfUserHasRole($need_role)
	{
		return (strtolower($need_role)==strtolower($this->have_role->name)) ? true : false;
	}

	public function hasStadium($stadium)
    {
		$this->have_stadium = $this->getUserStadium();

		if(is_array($stadium)){
			foreach($stadium as $need_stadium){
				if($this->checkIfUserHasStadium($need_stadium)) {
					return true;
				}
			}
		} else{
			return $this->checkIfUserHasStadium($stadium);
		}
		return false;
    }

	private function getUserStadium()
	{
		return $this->stadium()->getResults();
	}

	private function checkIfUserHasStadium($need_stadium)
	{
		return ($need_stadium==$this->have_stadium->name) ? true : false;
	}
}

