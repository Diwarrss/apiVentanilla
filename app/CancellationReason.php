<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cancellationReason extends Model
{
  /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cancellation_reasons';

    protected $fillable = [
        'name',
        'state',
        'user_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
      'id' => 'integer',
      'state' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
