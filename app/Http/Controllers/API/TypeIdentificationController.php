<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestTypeIdentification;
use App\TypeIdentification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TypeIdentificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return TypeIdentification::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestTypeIdentification $request)
    {
      try {
        DB::beginTransaction();

        $data = $request->all();
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $typeIdentification = TypeIdentification::create($data);
        DB::commit();

        if ($typeIdentification) {
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $typeIdentification
          ], 202);
        }else{
          return response()->json([
            'type' => 'error',
            'message' => 'Error al guardar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {
        return response()->json([
          'type' => 'error',
          'message' => 'Error al guardar',
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      try {
        DB::beginTransaction();

        /* $data = $request->all(); */
        $typeIdentification = TypeIdentification::find($id);

        //validations
        $request->validate([
          'name' => 'required|max:200|unique:Type_identifications,name,' . $id,
          'initials' => 'required|max:5|unique:Type_identifications,initials,' . $id
        ]);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'type_identifications',
          'action' => 'update',
          'data_id' => $typeIdentification->id,
          'type_identification_id' => $typeIdentification->id,
          'all_data' => json_encode($typeIdentification),
          'user_id' => Auth::user()->id
        ]);

        $typeIdentification->name = $request->name;
        $typeIdentification->initials = $request->initials;
        $typeIdentification->state = $request->state;
        $typeIdentification->save();

        DB::commit(); //commit de la transaccion

        if ($typeIdentification) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $typeIdentification
          ], 202);
        }else{
          return response()->json([
            'type' => 'error',
            'message' => 'Error al actualizar',
            'data' => []
          ], 204);
        }

      } catch (Exception $e) {
        return response()->json([
          'type' => 'error',
          'message' => 'Error al actualizar',
          'data' => []
        ], 204);
        DB::rollBack(); //si hay error no ejecute la transaccion
      }
    }

    public function updateState(Request $request, $id)
    {
      try {
        DB::beginTransaction();

        /* $data = $request->all(); */
        $typeIdentification = TypeIdentification::find($id);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'type_identifications',
          'action' => $typeIdentification->state ? 'disable' : 'enable',
          'data_id' => $typeIdentification->id,
          'type_identification_id' => $typeIdentification->id,
          'all_data' => json_encode($typeIdentification),
          'user_id' => Auth::user()->id
        ]);

        $typeIdentification->state = !$typeIdentification->state;
        $typeIdentification->save();

        DB::commit(); //commit de la transaccion

        if ($typeIdentification) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $typeIdentification->state
          ], 202);
        }else{
          return response()->json([
            'type' => 'error',
            'message' => 'Error al actualizar',
            'data' => []
          ], 204);
        }

      } catch (Exception $e) {
        return response()->json([
          'type' => 'error',
          'message' => 'Error al actualizar',
          'data' => []
        ], 204);
        DB::rollBack(); //si hay error no ejecute la transaccion
      }
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
}
