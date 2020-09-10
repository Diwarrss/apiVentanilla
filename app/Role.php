<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    /**
     *
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'guard_name'
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

    public function permissions()
    {
        return $this->belongsToMany('App\Permission', 'role_has_permissions', 'role_id', 'permission_id');
    }

    /* public function rolHasPermission()
    {
        return $this->belongsToMany('App\Role');
    } */
}
