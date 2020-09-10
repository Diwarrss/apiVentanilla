<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'slug',
        'initials',
        'nit',
        'address',
        'phone',
        'image',
        'logo',
        'state',
        'type',
        'user_id',
        'legal_representative_id'
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
        'legal_representative_id' => 'integer',
    ];


    public function legalRepresentative()
    {
        return $this->belongsTo(\App\LegalRepresentative::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
