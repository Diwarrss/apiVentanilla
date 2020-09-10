<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntryFilingHasDependence extends Model
{
  public $timestamps = false;
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'entry_filing_has_dependences';

  protected $fillable = [
    'entry_filing_id',
    'dependence_id'
  ];

  protected $casts = [
    'entry_filing_id' => 'integer',
    'dependence_id' => 'integer',
  ];
}
