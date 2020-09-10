<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class canceledEntryFiling extends Model
{
  /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'canceled_entry_filings';

    protected $fillable = [
        'description',
        'cancellationReason_id',
        'entryFiling_id',
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
        'id' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function entryFiling()
    {
        return $this->belongsTo(\App\entryFiling::class);
    }
}
