<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tmp_Customer_Stadium extends Model
{
    protected $table = 'tmp_customer_stadium';

    public function stadium()
    {
        return $this->belongsTo('App\Stadium');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }
}
