<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCampus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_campuses';

    protected $fillable = [
        'state',
        'campus_id',
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
        'campus_id' => 'integer',
        'user_id' => 'integer',
    ];


    public function campus()
    {
        return $this->belongsTo(\App\Campus::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
