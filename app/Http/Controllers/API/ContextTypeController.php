<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\ContextType;
use App\Exports\ContextTypeExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestContextType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContextTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      //retorna la información de la base de datos
      return ContextType::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestContextType $request)
    {
      //Guarda la informacion del nuevo registro
      try {
        DB::beginTransaction();

        //captura los parametros q vienen en la petición
        $data = $request->all();
        $data['slug'] = Str::slug($request->name,'-');
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $contextType = ContextType::create($data);
        DB::commit(); //commit de la transaccion

        if ($contextType) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $contextType
          ], 201);
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
      //actualiza la infomracion del registro especifico
      try {
        DB::beginTransaction();

        //Busca registro por ID
        $contextType =ContextType::find($id);

        //validations->valida q no este creado con el mismo nombre
        $request->validate([
          'name' => 'required|max:200|unique:context_types,name,' . $id
        ]);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'context_types',
          'action' => 'update',
          'data_id' => $contextType->id,
          'context_type_id' => $contextType->id,
          'all_data' => json_encode($contextType),
          'user_id' => Auth::user()->id
        ]);

        $contextType->name = $request->name;
        $contextType->slug = Str::slug($request->name,'-');
        $contextType->state = $request->state;
        //actualiza la infomracion del registro especifico
        $contextType->save();

        DB::commit(); //commit de la transaccion

        if ($contextType) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $contextType
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

    public function updateState(Request $request, $id)
    {
      //cambiar el estado del registro
      try {
        DB::beginTransaction();

        //Busca registro por ID
        $contextType = ContextType::find($id);

        //cambia el estado del registro
        if ($contextType->state) {
          $contextType->state = false;
        }else {
          $contextType->state = true;
        }
        //guarda el estado del registro
        $contextType->save();

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'context_types',
          'action' => $contextType->state ? 'disable' : 'enable',
          'data_id' => $contextType->id,
          'context_type_id' => $contextType->id,
          'all_data' => json_encode($contextType),
          'user_id' => Auth::user()->id
        ]);

        DB::commit(); //commit de la transaccion

        if ($contextType) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $contextType->state
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
      return (new ContextTypeExport)->download('ContextTypes.csv', \Maatwebsite\Excel\Excel::CSV);

      //return Excel::download(new TypeDocumentExport, 'TypeDocuments.csv');
    }

    public function dataExport()
    {
      return ContextType::select('id as ID', 'name as Nombre', DB::raw("(CASE state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"),)
                              ->get();
    }
}
