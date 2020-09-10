<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutgoingFilingHasPeople extends Model
{
  public $timestamps = false;
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'outgoing_filing_has_people';

  protected $fillable = [
    'outgoing_filing_id',
    'people_id'
  ];

  protected $casts = [
    'outgoing_filing_id' => 'integer',
    'people_id' => 'integer',
  ];
}
