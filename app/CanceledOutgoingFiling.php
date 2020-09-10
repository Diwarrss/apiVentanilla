<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class canceledOutgoingFiling extends Model
{
  /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'canceled_outgoing_filings';

    protected $fillable = [
        'description',
        'state',
        'cancellationReason_id',
        'outgoingFiling_id',
        'user_id'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

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

    public function outgoingFiling()
    {
        return $this->belongsTo(\App\OutgoingFiling::class);
    }
}
