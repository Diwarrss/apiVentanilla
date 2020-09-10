<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpFile extends Model
{
  protected $table = 'up_files';

  protected $fillable = [
    'name',
    'type',
    'url',
    'fileable_type',
    'fileable_id'
  ];

  //es una tabla polimorfica
  public function fileable()
  {
    return $this->morphTo();
  }

}
