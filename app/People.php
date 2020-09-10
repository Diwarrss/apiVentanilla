<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'people';

    protected $fillable = [
        'identification',
        'names',
        'telephone',
        'address',
        'email',
        'state',
        'type',
        'type_identification_id',
        'gender_id',
        'people_id',
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
        'type_identification_id' => 'integer',
        'gender_id' => 'integer',
        'people_id' => 'integer',
    ];


    public function typeIdentification()
    {
        return $this->belongsTo(\App\TypeIdentification::class);
    }

    public function gender()
    {
        return $this->belongsTo(\App\Gender::class);
    }

    public function people()
    {
        return $this->belongsTo(\App\People::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function outgoingFiling()
    {
        return $this->belongsToMany(\App\OutgoingFiling::class);
    }
}
