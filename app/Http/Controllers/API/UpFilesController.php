<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Session;
use Illuminate\Support\Facades\Redirect;
use App\Helper;

class UpFilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Download file.
     *
     * @param type    $type     file type
     * @param string  $filename file typname
     * @param integer $settled       settled
     *
     * @access public
     *
     * @return \Illuminate\Http\Response
     */
    public function getFile($type, $filename, $settled )
    {
      $path = Helper::PublicPath() . '\\storage\\uploads\\'. $type . '\\' . $settled . '\\' . $filename;
      $header = [
          'Content-Type' => 'application/*',
      ];
      return response()->download($path, $filename, $header);
    }
}
