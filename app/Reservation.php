<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservation';
    
    public function field()
    {
        return $this->belongsTo('App\Field');
    }

    public function customer()
    {
        return $this->hasOne('App\Customer', 'id', 'customer_id');
    }
}
