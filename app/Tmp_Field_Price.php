<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tmp_Field_Price extends Model
{
    protected $table = 'tmp_field_price';

    public function field()
    {
        return $this->belongsTo('App\Field');
    }
}
