<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dependence extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dependences';

    protected $fillable = [
        'identification',
        'names',
        'slug',
        'telephone',
        'address',
        'state',
        'type',
        'email',
        'attachments',
        'type_identification_id',
        'gender_id',
        'user_id'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
      /* 'id' */
    ];

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
        'user_id' => 'integer',
    ];

    public function typeIdentification()
    {
        return $this->belongsTo(\App\TypeIdentification::class);
    }

    public function gender()
    {
        return $this->belongsTo(\App\Gender::class);
    }

    public function typePerson()
    {
        return $this->belongsTo(\App\TypePeople::class, 'type');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function entryFiling()
    {
        return $this->belongsToMany(\App\EntryFiling::class);
    }

    public function outgoingFiling()
    {
        return $this->belongsToMany(\App\OutgoingFiling::class);
    }

}
