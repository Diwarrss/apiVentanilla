<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Exports\GenderExport;
use App\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestGender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Gender::all();//retorna la información de la base de datos
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestGender $request)
    {
      try {//Guarda la informacion del nuevo registro
        DB::beginTransaction();

        $data = $request->all();//captura los parametros q vienen en la petición
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $gender = Gender::create($data);//Guarda la informacion del registro
        DB::commit();//commit de la transaccion

        if ($gender) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $gender
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
    {//actualiza la infomracion del registro especifico
      try {
        DB::beginTransaction();

        //Busca registro por ID
        $gender = Gender::find($id);

        //validations->valida que el nombre y las iniciales no esten usadas en otro registro
        $request->validate([
          'name' => 'required|max:200|unique:genders,name,' . $id,
          'initials' => 'required|max:5|unique:genders,initials,' . $id
        ]);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'genders',
          'action' => 'update',
          'data_id' => $gender->id,
          'gender_id' => $gender->id,
          'all_data' => json_encode($gender),
          'user_id' => Auth::user()->id
        ]);

        $gender->name = $request->name;
        $gender->initials = $request->initials;
        $gender->state = $request->state;
        $gender->save();//Guarda la informacion del registro

        DB::commit(); //commit de la transaccion

        if ($gender) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $gender
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
    {//cambiar el estado del registro
      try {
        DB::beginTransaction();

        //Busca registro por ID
        $gender = Gender::find($id);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'genders',
          'action' => $gender->state ? 'disable' : 'enable',
          'data_id' => $gender->id,
          'gender_id' => $gender->id,
          'all_data' => json_encode($gender),
          'user_id' => Auth::user()->id
        ]);

        $gender->state = !$gender->state;//cambia el estado del registro
        $gender->save();//guarda el estado del registro

        DB::commit(); //commit de la transaccion

        if ($gender) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $gender->state
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
      return (new GenderExport)->download('Genders.csv', \Maatwebsite\Excel\Excel::CSV);

      //return Excel::download(new TypeDocumentExport, 'TypeDocuments.csv');
    }
}
