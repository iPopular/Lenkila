<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    protected $table = 'stadium';

    public static function owner()
    {
      return static::leftJoin(
        'users',
        'stadium.id', '=', 'users.stadium_id'
      )->leftJoin(
        'roles',
        'users.role_id', '=', 'roles.id'
      )->where('role_id', '>=', 3)
      ->select('users.id as id', 'users.firstname','users.lastname','users.username', 'users.email', 'stadium.id as stadium_id', 'stadium.name as stadium_name', 'roles.id as role_id','roles.name as role_name');
    }

    public function users()
    {
        return $this->hasMany('App\User','stadium_id');
    }

    public function tmp_customer_stadium()
    {
        return $this->hasMany('App\Tmp_Customer_Stadium','stadium_id');
    }

    public function field()
    {
        return $this->hasMany('App\Field','stadium_id')->orderBy('status', 'DESC')->orderBy('id', 'ASC');
    }

    public function promotions()
    {
        return $this->hasMany('App\Promotions','stadium_id');
    }

    public function holidays()
    {
        return $this->hasMany('App\Holidays','stadium_id')->orderBy('start_date', 'ASC');
    }
}
