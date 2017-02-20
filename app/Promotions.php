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

    public static function checkOverlap($stadiumId, $starttime, $endtime, $startDate, $endDate, $promotionsId = 0)
    {
      return static::leftJoin(
        'stadium',
        'promotions.stadium_id', '=', 'stadium.id'
      )->where('stadium_id', '=', $stadiumId)
      ->where('promotions.id', '!=', $promotionsId)
      ->where('promotions.start_time', '<', $endtime)
      ->where('promotions.end_time', '>', $starttime)
      ->where('promotions.start_date', '<', $endDate)
      ->where('promotions.end_date', '>', $startDate)
      ->select('promotions.id');
    }
}
