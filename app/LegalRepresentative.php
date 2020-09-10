<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LegalRepresentative extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'legal_representatives';

    protected $fillable = [
        'document',
        'name',
        'phone',
        'address',
        'email',
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
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
