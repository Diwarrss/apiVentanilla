<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Dependence;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestDependence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DependenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
        if ($request->active === '0') {
          return Dependence::all();
        }else if ($request->active === '1') {
          return Dependence::where('state', 1)->get();
        }else if ($request->active === '2') {
          return Dependence::where([['state', 1 ], [ 'type', 'dependence' ]])->get();
        }else if ($request->active === '3') {
          return Dependence::where([['state', 1 ], [ 'type', 'person' ]])->get();
        }

        //return Dependence::with('TypeIdentification', 'Gender')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestDependence $request)
    {
      try {
        DB::beginTransaction();

        $data = $request->all();
        $data['slug'] = ($request->type == 'dependence') ? Str::slug($request->names,'-') : null;
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $dependence = Dependence::create($data);

        DB::commit(); //commit de la transaccion

        if ($dependence) {
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $dependence
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
        $dependence = Dependence::find($id);
        //validations
        $request->validate([
          'identification' => 'nullable|max:20|unique:dependences,identification,' . $id
        ]);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'dependences',
          'action' => 'update',
          'data_id' => $dependence->id,
          'dependence_id' => $dependence->id,
          'all_data' => json_encode($dependence),
          'user_id' => Auth::user()->id
        ]);

        $dependence->identification = $request->identification;
        $dependence->names = $request->names;
        $dependence->slug = ($request->type == 'dependence') ? Str::slug($request->names,'-') : null;
        $dependence->telephone = $request->telephone;
        $dependence->address = $request->address;
        $dependence->state = $request->state;
        $dependence->type = $request->type;
        $dependence->attachments = $request->attachments;
        $dependence->dependence_id = $request->dependence_id;
        $dependence->type_identification_id = $request->type_identification_id;
        $dependence->gender_id = $request->gender_id;
        $dependence->save();
        DB::commit(); //commit de la transaccion

        if ($dependence) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $dependence
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
        $dependence = Dependence::find($id);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'dependences',
          'action' => $dependence->state ? 'disable' : 'enable',
          'data_id' => $dependence->id,
          'dependence_id' => $dependence->id,
          'all_data' => json_encode($dependence),
          'user_id' => Auth::user()->id
        ]);

        $dependence->state = !$dependence->state;
        $dependence->save();

        DB::commit(); //commit de la transaccion

        if ($dependence) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $dependence->state
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
