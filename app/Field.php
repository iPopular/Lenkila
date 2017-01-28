<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $table = 'field';

    public function stadium()
    {
        return $this->belongsTo('App\Stadium');
    }

    public function reservation()
    {
        return $this->hasMany('App\Reservation', 'field_id');
    }

    public function tmp_field_price()
    {
        return $this->hasMany('App\Tmp_Field_Price', 'field_id');
    }
}
