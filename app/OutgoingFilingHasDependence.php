<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutgoingFilingHasDependence extends Model
{
  public $timestamps = false;
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'outgoing_filing_has_dependences';

  protected $fillable = [
    'outgoing_filing_id',
    'dependence_id'
  ];

  protected $casts = [
    'outgoing_filing_id' => 'integer',
    'dependence_id' => 'integer',
  ];
}
