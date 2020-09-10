<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
  /**
   * Display a listing of roles from current logged user.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function __invoke()
  {
    return auth()->user()->getRoleNames();
  }
}
