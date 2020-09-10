<?php

namespace App\Http\Controllers\API;

use App\Audit;
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
        return Gender::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestGender $request)
    {
      try {
        DB::beginTransaction();

        $data = $request->all();
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $gender = Gender::create($data);
        DB::commit();

        if ($gender) {
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $gender
          ], 202);
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
        $gender = Gender::find($id);

        //validations
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
        $gender->save();

        DB::commit(); //commit de la transaccion

        if ($gender) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $gender
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

        $gender->state = !$gender->state;
        $gender->save();

        DB::commit(); //commit de la transaccion

        if ($gender) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $gender->state
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
