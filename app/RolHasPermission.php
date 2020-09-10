<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolHasPermission extends Model
{
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_has_permissions';

    protected $fillable = [
      'role_id',
      'permission_id'
    ];

    protected $casts = [
      'role_id' => 'integer',
      'permission_id' => 'integer',
    ];

    public function hasPermission()
    {
      return $this->belongsTo(\App\Permission::class,'permission_id');
    }
}
