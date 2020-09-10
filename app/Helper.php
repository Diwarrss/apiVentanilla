<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
  /**
   * Get public path
   *
   * @return \Illuminate\Http\Response
   */
  public static function publicPath()
  {
      $path = public_path();
      if (isset($_SERVER["SERVER_NAME"]) && $_SERVER["SERVER_NAME"] != '127.0.0.1') {
        $path = getcwd();
      }
      return $path;
  }
}
