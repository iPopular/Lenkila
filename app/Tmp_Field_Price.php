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

    public static function checkOverlap($fieldId, $starttime, $endtime, $day, $fieldpriceId = 0)
    {
      return static::leftJoin(
        'field',
        'tmp_field_price.field_id', '=', 'field.id'
      )->where('field_id', '=', $fieldId)
      ->where('tmp_field_price.id', '!=', $fieldpriceId)
      ->where('tmp_field_price.start_time', '<', $endtime)
      ->where('tmp_field_price.end_time', '>', $starttime)
      ->where('tmp_field_price.day', 'like', '%' . $day . '%')
      ->select('tmp_field_price.id');
    }
}
