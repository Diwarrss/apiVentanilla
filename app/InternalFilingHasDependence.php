<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InternalFilingHasDependence extends Model
{
  public $timestamps = false;
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'internal_filing_has_dependences';

  protected $fillable = [
    'internal_filing_id',
    'dependence_id'
  ];

  protected $casts = [
    'internal_filing_id' => 'integer',
    'dependence_id' => 'integer',
  ];
}
