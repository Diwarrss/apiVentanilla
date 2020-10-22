<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\cancellationReason;
use App\Exports\CancellationReasonExport;
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
      //retorna la información de la base de datos
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
      //Guarda la informacion del nuevo motivo de cancelación
      try {
        DB::beginTransaction();

        $data = $request->all();
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $cancellationReason = cancellationReason::create($data);
        DB::commit(); //commit de la transaccion

        if ($cancellationReason) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $cancellationReason
          ], 202);
        }else{ //respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al guardar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) { //error en el proceso
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
      //actualiza la infomracion del motivo de cancelación especifico
      try {
        DB::beginTransaction();

        //Busca el motivo de cancelación por ID
        $cancellationReason = cancellationReason::find($id);

        //validations-valida q no exista el motivo de cancelacion por nombre
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

        //guarda los datos q se actualizaron
        $cancellationReason->name = $request->name;
        $cancellationReason->state = $request->state;
        $cancellationReason->save();

        DB::commit(); //commit de la transaccion


        if ($cancellationReason) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $cancellationReason
          ], 202);
        }else{ //respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al actualizar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) { //error en el proceso
        return response()->json([
          'type' => 'error',
          'message' => 'Error al actualizar',
          'data' => []
        ], 204);
        DB::rollBack(); //si hay error no ejecute la transaccion
      }
    }

    public function updateState($id)
    {
      //cambia el estado del registro
      try {
        DB::beginTransaction();

        //Busca el motivo de cancelación por ID
        $cancellationReason = cancellationReason::find($id);
        //return $cancellationReason;
        //guarda el estado del registro
        if ($cancellationReason->state) {

          $cancellationReason->state = false;
        }else {
          $cancellationReason->state = true;
        }
        $cancellationReason->save();

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'cancellation_reasons',
          'action' => $cancellationReason->state ? 'disable' : 'enable',
          'data_id' => $cancellationReason->id,
          'cancellation_reason_id' => $cancellationReason->id,
          'all_data' => json_encode($cancellationReason),
          'user_id' => Auth::user()->id
        ]);

        DB::commit(); //commit de la transaccion

        if ($cancellationReason) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $cancellationReason->state
          ], 202);
        }else{ //respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al actualizar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) { //error en el proceso
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

    //download csv
    public function export()
    {
      return (new CancellationReasonExport)->download('TypeDocuments.csv', \Maatwebsite\Excel\Excel::CSV);

      //return Excel::download(new TypeDocumentExport, 'TypeDocuments.csv');
    }
}
