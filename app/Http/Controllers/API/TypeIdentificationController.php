<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Exports\TypeIdentificationExport;
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
      return TypeIdentification::all();//retorna la información de la base de datos
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestTypeIdentification $request)
    {//Guarda la informacion del nuevo registro
      try {
        DB::beginTransaction();

        $data = $request->all();//captura los parametros q vienen en la petición
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $typeIdentification = TypeIdentification::create($data);//Guarda la informacion del registro
        DB::commit();

        if ($typeIdentification) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $typeIdentification
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

        $typeIdentification = TypeIdentification::find($id);//Busca registro por ID

        //validations->valida que el nombre y las iniciales no esten creadas en la base de datos
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

        //actualiza la infomracion del registro especifico
        $typeIdentification->name = $request->name;
        $typeIdentification->initials = $request->initials;
        $typeIdentification->state = $request->state;
        $typeIdentification->save();//Guarda la informacion del registro

        DB::commit(); //commit de la transaccion

        if ($typeIdentification) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $typeIdentification
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

        $typeIdentification = TypeIdentification::find($id);//Busca registro por ID

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'type_identifications',
          'action' => $typeIdentification->state ? 'disable' : 'enable',
          'data_id' => $typeIdentification->id,
          'type_identification_id' => $typeIdentification->id,
          'all_data' => json_encode($typeIdentification),
          'user_id' => Auth::user()->id
        ]);

        $typeIdentification->state = !$typeIdentification->state;//cambia el estado del registro
        $typeIdentification->save();//guarda el estado del registro

        DB::commit(); //commit de la transaccion

        if ($typeIdentification) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $typeIdentification->state
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
      return (new TypeIdentificationExport)->download('TypeIdentifications.csv', \Maatwebsite\Excel\Excel::CSV);

      //return Excel::download(new TypeDocumentExport, 'TypeDocuments.csv');
    }
}
