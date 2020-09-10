<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeDocument extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'type_documents';

    protected $fillable = [
      'name',
      'slug',
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
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
