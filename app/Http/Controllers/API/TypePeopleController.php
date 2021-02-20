<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Exports\TypePeopleExport;
use App\Http\Controllers\Controller;
use App\TypePeople;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TypePeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return TypePeople::orderBy('name', 'asc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      try {
        DB::beginTransaction();

        $data = $request->all();//captura los parametros q vienen en la petición
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $typePeople = TypePeople::create($data);//Guarda la informacion del nuevo registro
        DB::commit(); //commit de la transaccion

        if ($typePeople) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $typePeople
          ], 201);
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
    {
      try {
        DB::beginTransaction();

        $typePeople = TypePeople::find($id);//captura los parametros q vienen en la petición

        //valida que el nombre no este creado en la base de datos
        $request->validate([
          'name' => 'required|max:100|unique:type_people,name,' . $id
        ]);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'type_people',
          'action' => 'update',
          'data_id' => $typePeople->id,
          'type_people_id' => $typePeople->id,
          'all_data' => json_encode($typePeople),
          'user_id' => Auth::user()->id
        ]);

        //actualiza la infomracion del registro especifico
        $typePeople->name = $request->name;
        $typePeople->state = $request->state;
        $typePeople->type = $request->type;
        $typePeople->save();//Guarda la informacion del registro

        DB::commit(); //commit de la transaccion

        if ($typePeople) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $typePeople
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

        $typePeople = TypePeople::find($id);//Busca registro por ID

        //cambia el estado del registro
        if ($typePeople->state) {
          $typePeople->state = false;
        }else {
          $typePeople->state = true;
        }
        $typePeople->save();//guarda el estado del registro

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'type_people',
          'action' => $typePeople->state ? 'disable' : 'enable',
          'data_id' => $typePeople->id,
          'type_people_id' => $typePeople->id,
          'all_data' => json_encode($typePeople),
          'user_id' => Auth::user()->id
        ]);

        DB::commit(); //commit de la transaccion

        if ($typePeople) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $typePeople->state
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
      return (new TypePeopleExport)->download('TypeDocuments.csv', \Maatwebsite\Excel\Excel::CSV);

      //return Excel::download(new TypeDocumentExport, 'TypeDocuments.csv');
    }

    public function dataExport()
    {
      return TypePeople::select('id as ID', 'name as Nombre', DB::raw("(CASE state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"),)
                              ->get();
    }
}
