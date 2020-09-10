<?php

namespace App\Http\Controllers\API;

use App\Audit;
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
        return Priority::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestPriority $request)
    {
      try {
        DB::beginTransaction();

        $data = $request->all();
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $priority = Priority::create($data);
        DB::commit();

        if ($priority) {
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $priority
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
        $priority = Priority::find($id);

        //validations
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

        $priority->name = $request->name;
        $priority->initials = $request->initials;
        $priority->state = $request->state;
        $priority->days = $request->days;
        $priority->save();

        DB::commit(); //commit de la transaccion

        if ($priority) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $priority
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
        $priority = Priority::find($id);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'priorities',
          'action' => $priority->state ? 'disable' : 'enable',
          'data_id' => $priority->id,
          'priority_id' => $priority->id,
          'all_data' => json_encode($priority),
          'user_id' => Auth::user()->id
        ]);

        $priority->state = !$priority->state;
        $priority->save();

        DB::commit(); //commit de la transaccion

        if ($priority) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $priority->state
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
