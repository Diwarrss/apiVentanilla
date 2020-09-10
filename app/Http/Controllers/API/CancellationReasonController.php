<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\cancellationReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestCancellationReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CancellationReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return cancellationReason::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestCancellationReason $request)
    {
      try {
        DB::beginTransaction();

        $data = $request->all();
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $cancellationReason = cancellationReason::create($data);
        DB::commit();

        if ($cancellationReason) {
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $cancellationReason
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
        $cancellationReason = cancellationReason::find($id);

        //validations
        $request->validate([
          'name' => 'required|max:200|unique:cancellation_reasons,name,' . $id
        ]);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'cancellation_reasons',
          'action' => 'update',
          'data_id' => $cancellationReason->id,
          'cancellation_reason_id' => $cancellationReason->id,
          'all_data' => json_encode($cancellationReason),
          'user_id' => Auth::user()->id
        ]);

        $cancellationReason->name = $request->name;
        $cancellationReason->state = $request->state;
        $cancellationReason->save();

        DB::commit(); //commit de la transaccion

        if ($cancellationReason) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $cancellationReason
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
        $cancellationReason = cancellationReason::find($id);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'cancellation_reasons',
          'action' => $cancellationReason->state ? 'disable' : 'enable',
          'data_id' => $cancellationReason->id,
          'cancellation_reason_id' => $cancellationReason->id,
          'all_data' => json_encode($cancellationReason),
          'user_id' => Auth::user()->id
        ]);

        $cancellationReason->state = !$cancellationReason->state;
        $cancellationReason->save();

        DB::commit(); //commit de la transaccion

        if ($cancellationReason) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $cancellationReason->state
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
