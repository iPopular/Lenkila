<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    protected $table = 'stadium';

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
        return $this->hasMany('App\Field','stadium_id');
    }

    public function promotions()
    {
        return $this->hasMany('App\Promotions','stadium_id');
    }
}
