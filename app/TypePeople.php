<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypePeople extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'type_people';

    protected $fillable = [
      'name',
      'type',
      'state',
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
        'type' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
