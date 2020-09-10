<?php

namespace App\Http\Controllers\API;

use App\Audit;
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
    {
      if ($request->active === 'false') {
        return People::all();
      }
      return People::where('state', 1)->get();
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

        $data = $request->all();
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $people = People::create($data);

        DB::commit(); //commit de la transaccion

        if ($people) {
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $people
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
        $people = People::find($id);

        //validations
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

        if ($people) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $people
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
        $people = People::find($id);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'people',
          'action' => $people->state ? 'disable' : 'enable',
          'data_id' => $people->id,
          'people_id' => $people->id,
          'all_data' => json_encode($people),
          'user_id' => Auth::user()->id
        ]);

        $people->state = !$people->state;
        $people->save();

        DB::commit(); //commit de la transaccion

        if ($people) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $people->state
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
