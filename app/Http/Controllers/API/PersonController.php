<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Exports\PeopleExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestPeople;
use App\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {//retorna la información de la base de datos
      if ($request->active === 'false') {//toda la iformación de la abse de datos
        return People::all();
      }
      return People::where('state', 1)->get();//retorna la los registros con estado 1 (activos)
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestPeople $request)
    {
      try {
        DB::beginTransaction();

        $data = $request->all();//captura los parametros q vienen en la petición
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $people = People::create($data);//Guarda la informacion del nuevo registro

        DB::commit(); //commit de la transaccion

        if ($people) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $people
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
    {//actualiza la infomracion del registro especifico
      try {
        DB::beginTransaction();

        $people = People::find($id);//Busca registro por ID
        //validations->valida q no exista el registro con la misma identificación
        $request->validate([
          'identification' => 'required|max:20|unique:people,identification,' . $id
        ]);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'people',
          'action' => 'update',
          'data_id' => $people->id,
          'people_id' => $people->id,
          'all_data' => json_encode($people),
          'user_id' => Auth::user()->id
        ]);

        //actualiza la infomracion del registro especifico
        $people->identification = $request->identification;
        $people->names = $request->names;
        $people->telephone = $request->telephone;
        $people->address = $request->address;
        $people->email = $request->email;
        $people->state = $request->state;
        $people->type = $request->type;
        $people->people_id = $request->people_id;
        $people->type_identification_id = $request->type_identification_id;
        $people->gender_id = $request->gender_id;
        $people->save();
        DB::commit(); //commit de la transaccion

        if ($people) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $people
          ], 202);
        }else{//respuesta de error
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
    {//cambiar el estado del registro
      try {
        DB::beginTransaction();

        $people = People::find($id);//Busca registro por ID

        //cambia el estado del registro
        if ($people->state) {
          $people->state = false;
        }else {
          $people->state = true;
        }
        $people->save();//guarda el estado del registro

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'people',
          'action' => $people->state ? 'disable' : 'enable',
          'data_id' => $people->id,
          'people_id' => $people->id,
          'all_data' => json_encode($people),
          'user_id' => Auth::user()->id
        ]);

        DB::commit(); //commit de la transaccion

        if ($people) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $people->state
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
      return (new PeopleExport)->download('People.csv', \Maatwebsite\Excel\Excel::CSV);

      //return Excel::download(new TypeDocumentExport, 'TypeDocuments.csv');
    }

    public function dataExport()
    {
      return People::select('id as ID', 'identification as Identificación', 'names as Nombre', 'telephone as Telefono', 'address as Dirección', 'email as Email', DB::raw("(CASE state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), DB::raw("(CASE type WHEN 'person' THEN 'Persona' ELSE 'Compañia' END) AS Tipo"))
                      ->get();
    }
}
