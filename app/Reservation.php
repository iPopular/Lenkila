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

    public static function checkOverlap($fieldId, $reserveStarttime, $reserveEndttime, $reservationId = 0)
    {
      return static::leftJoin(
        'field',
        'reservation.field_id', '=', 'field.id'
      )->where('field_id', '=', $fieldId)
      ->where('reservation.id', '!=', $reservationId)
      ->where('reservation.start_time', '<', $reserveEndttime)
      ->where('reservation.end_time', '>', $reserveStarttime)
      ->select('reservation.id');
    }
}
