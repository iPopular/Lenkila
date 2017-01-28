<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotions extends Model
{
    protected $table = 'promotions';

    public function stadium()
    {
        return $this->belongsTo('App\Stadium');
    }
}
