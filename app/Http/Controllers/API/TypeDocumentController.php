<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Exports\TypeDocumentExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestTypeDocument;
use App\TypeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Excel;

class TypeDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return TypeDocument::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestTypeDocument $request)
    {//retorna la información de la base de datos
      try {
        DB::beginTransaction();

        $data = $request->all();//captura los parametros q vienen en la petición
        $data['slug'] = Str::slug($request->name,'-');
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $typeDocument = TypeDocument::create($data);//Guarda la informacion del nuevo registro
        DB::commit(); //commit de la transaccion

        if ($typeDocument) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $typeDocument
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

        $typeDocument = TypeDocument::find($id);//captura los parametros q vienen en la petición

        //valida que el nombre no este creado en la base de datos
        $request->validate([
          'name' => 'required|max:100|unique:type_documents,name,' . $id
        ]);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'type_documents',
          'action' => 'update',
          'data_id' => $typeDocument->id,
          'type_document_id' => $typeDocument->id,
          'all_data' => json_encode($typeDocument),
          'user_id' => Auth::user()->id
        ]);

        //actualiza la infomracion del registro especifico
        $typeDocument->name = $request->name;
        $typeDocument->slug = Str::slug($request->name,'-');
        $typeDocument->state = $request->state;
        $typeDocument->save();//Guarda la informacion del registro

        DB::commit(); //commit de la transaccion

        if ($typeDocument) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $typeDocument
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

        $typeDocument = TypeDocument::find($id);//Busca registro por ID

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'type_documents',
          'action' => $typeDocument->state ? 'disable' : 'enable',
          'data_id' => $typeDocument->id,
          'type_document_id' => $typeDocument->id,
          'all_data' => json_encode($typeDocument),
          'user_id' => Auth::user()->id
        ]);

        $typeDocument->state = !$typeDocument->state;//cambia el estado del registro
        $typeDocument->save();//guarda el estado del registro

        DB::commit(); //commit de la transaccion

        if ($typeDocument) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $typeDocument->state
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
      return (new TypeDocumentExport)->download('TypeDocuments.csv', \Maatwebsite\Excel\Excel::CSV);

      //return Excel::download(new TypeDocumentExport, 'TypeDocuments.csv');
    }
}
