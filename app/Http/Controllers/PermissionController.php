<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermissionController extends Controller
{
  /**
   * Display a listing of permissions from current logged user.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function __invoke()
  {
    return auth()->user()->getAllPermissions()->pluck('name');
  }
}
