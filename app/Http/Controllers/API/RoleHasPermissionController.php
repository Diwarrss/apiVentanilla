<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\RolHasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
    {//asignar permiso a determinado rol
      try {
        DB::beginTransaction();

        //Guarda la informacion del nuevo registro
        $rhp = RolHasPermission::create([
          'role_id' => $request->role_id,
          'permission_id' => $request->permission_id
        ]);
        DB::commit();

        if ($rhp) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Permiso Asignado con éxito',
            'notify' => true,
            'data' => $rhp
          ], 202);
        }else{//respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al asignar',
            'notify' => true,
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {//error en el proceso
        return response()->json([
          'type' => 'error',
          'message' => 'Error al asignar',
          'data' => []
        ], 204);
        DB::rollBack(); //si hay error no ejecute la transaccion
      }
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function destroy(Request $request)
    {//eliminar permiso de determinado rol
      try {
        DB::beginTransaction();

        $rhp = RolHasPermission::where([['permission_id', $request->permission_id] , ['role_id', $request->role_id]]);
        $rhp->delete();//elimina el permiso  de un rol especifico
        DB::commit();

        if ($rhp) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Permiso Eliminado con éxito',
            'data' => $rhp
          ], 202);
        }else{//respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al eliminar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {//error en el proceso
        return response()->json([
          'type' => 'error',
          'message' => 'Error al eliminar',
          'data' => []
        ], 204);
        DB::rollBack(); //si hay error no ejecute la transaccion
      }
    }
}
