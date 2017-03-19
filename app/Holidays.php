<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holidays extends Model
{
    protected $table = 'holidays';

    public function stadium()
    {
        return $this->belongsTo('App\Stadium');
    }
}
