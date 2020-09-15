<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Exports\PriorityExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestPriority;
use App\Priority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Priority::all();//retorna la información de la base de datos
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestPriority $request)
    {//Guarda la informacion del nuevo registro
      try {
        DB::beginTransaction();

        $data = $request->all();//captura los parametros q vienen en la petición
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $priority = Priority::create($data);//Guarda la informacion del nuevo registro
        DB::commit();//commit de la transaccion

        if ($priority) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $priority
          ], 202);
        }else{//respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al guardar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {//error en el proceso
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
    {//actualiza la infomracion del registro especifico
      try {
        DB::beginTransaction();

        $priority = Priority::find($id);//Busca registro por ID

        //validations->valida nombre e iniciales para no repetir por q son datos unicos del registro
        $request->validate([
          'name' => 'required|max:200|unique:priorities,name,' . $id,
          'initials' => 'required|max:5|unique:priorities,initials,' . $id
        ]);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'priorities',
          'action' => 'update',
          'data_id' => $priority->id,
          'priority_id' => $priority->id,
          'all_data' => json_encode($priority),
          'user_id' => Auth::user()->id
        ]);

        //actualiza la infomracion del registro especifico
        $priority->name = $request->name;
        $priority->initials = $request->initials;
        $priority->state = $request->state;
        $priority->days = $request->days;
        $priority->save();//Guarda la informacion del registro

        DB::commit(); //commit de la transaccion

        if ($priority) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $priority
          ], 202);
        }else{//respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al actualizar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {//error en el proceso
        return response()->json([
          'type' => 'error',
          'message' => 'Error al actualizar',
          'data' => []
        ], 204);
        DB::rollBack(); //si hay error no ejecute la transaccion
      }
    }

    public function updateState(Request $request, $id)
    {//cambiar el estado del registro
      try {
        DB::beginTransaction();

        $priority = Priority::find($id);//Busca registro por ID

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'priorities',
          'action' => $priority->state ? 'disable' : 'enable',
          'data_id' => $priority->id,
          'priority_id' => $priority->id,
          'all_data' => json_encode($priority),
          'user_id' => Auth::user()->id
        ]);

        $priority->state = !$priority->state;//cambia el estado del registro
        $priority->save();//guarda el estado del registro

        DB::commit(); //commit de la transaccion

        if ($priority) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $priority->state
          ], 202);
        }else{//respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al actualizar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {//error en el proceso
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
      return (new PriorityExport)->download('Priorities.csv', \Maatwebsite\Excel\Excel::CSV);

      //return Excel::download(new TypeDocumentExport, 'TypeDocuments.csv');
    }
}
