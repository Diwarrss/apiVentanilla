<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestTypeDocument;
use App\TypeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
    {
      try {
        DB::beginTransaction();

        $data = $request->all();
        $data['slug'] = Str::slug($request->name,'-');
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $typeDocument = TypeDocument::create($data);
        DB::commit(); //commit de la transaccion

        if ($typeDocument) {
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $typeDocument
          ], 201);
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
        $typeDocument = TypeDocument::find($id);

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

        $typeDocument->name = $request->name;
        $typeDocument->slug = Str::slug($request->name,'-');
        $typeDocument->state = $request->state;
        $typeDocument->save();

        DB::commit(); //commit de la transaccion

        if ($typeDocument) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $typeDocument
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
        $typeDocument = TypeDocument::find($id);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'type_documents',
          'action' => $typeDocument->state ? 'disable' : 'enable',
          'data_id' => $typeDocument->id,
          'type_document_id' => $typeDocument->id,
          'all_data' => json_encode($typeDocument),
          'user_id' => Auth::user()->id
        ]);

        $typeDocument->state = !$typeDocument->state;
        $typeDocument->save();

        DB::commit(); //commit de la transaccion

        if ($typeDocument) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $typeDocument->state
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
