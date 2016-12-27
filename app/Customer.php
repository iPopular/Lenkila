<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';

    public function tmp_customer_stadium()
    {
        return $this->hasMany('App\Tmp_Customer_Stadium', 'customer_id');
    }
}
