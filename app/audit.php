<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
  /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'audits';

    /* protected $fillable = [
        'table',
        'action',
        'all_info',
        'user_id'
    ]; */
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
      'id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
