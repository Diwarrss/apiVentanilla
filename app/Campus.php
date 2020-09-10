<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'campuses';

    protected $fillable = [
        'name',
        'initials',
        'address',
        'telephone',
        'state',
        'company_id',
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
        'company_id' => 'integer',
    ];


    public function company()
    {
        return $this->belongsTo(\App\Company::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
