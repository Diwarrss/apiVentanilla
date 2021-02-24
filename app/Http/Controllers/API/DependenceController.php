<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Dependence;
use App\Exports\DependenceExport;
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
        //retorna la información de la base de datos
        if ($request->active === '0') { //retorna toda la informacipon
          return Dependence::orderBy('names', 'asc')->with('typePerson')->get();
        }else if ($request->active === '1') { //retorna la información con estado 1->activo
          return Dependence::where('state', 1)->with('typePerson')->orderBy('names', 'asc')->get();
        }else if ($request->active === '2') { //retorna toda la información activa y con tipo depencencia
          return Dependence::with('typePerson')
            ->whereHas('typePerson', function($query) {//condicion en la relación
              //retorna las dependencias que son tipo entidad
              $query->where('type_people.type', 0);
            })
            ->where([['state', 1 ]])
            ->orderBy('names', 'asc')
            ->get();
        }else if ($request->active === '3') { //retorna toda la información activa y con tipo persona
          return Dependence::with('typePerson')
            ->whereHas('typePerson', function($query) {//condicion en la relación
              //retorna las dependencias que son tipo entidad
              $query->where('type_people.type', 1);
            })
            ->where([['state', 1 ]])
            ->orderBy('names', 'asc')
            ->get();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestDependence $request)
    {//Guarda la informacion del nuevo registro
      try {
        DB::beginTransaction();

        $data = $request->all();//captura los parametros q vienen en la petición
        $data['slug'] = ($request->type == 'dependence') ? Str::slug($request->names,'-') : null;
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        //Guarda la informacion del nuevo registro
        $dependence = Dependence::create($data);

        DB::commit(); //commit de la transaccion

        if ($dependence) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $dependence
          ], 201);
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
    {
      try {
        //actualiza la infomracion del registro especifico
        DB::beginTransaction();

        $dependence = Dependence::find($id);//Busca registro por ID
        //validations->valida q no exista el registro con la misma identificación
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

        //actualiza la infomracion del registro especifico
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
        //Guarda la informacion del registro
        $dependence->save();
        DB::commit(); //commit de la transaccion

        if ($dependence) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $dependence
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
    {
      //cambiar el estado del registro
      try {
        DB::beginTransaction();

        //Busca registro por ID
        $dependence = Dependence::find($id);

        //cambia el estado del registro
        if ($dependence->state) {
          $dependence->state = false;
        }else {
          $dependence->state = true;
        }
        //guarda el estado del registro
        $dependence->save();

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'dependences',
          'action' => $dependence->state ? 'disable' : 'enable',
          'data_id' => $dependence->id,
          'dependence_id' => $dependence->id,
          'all_data' => json_encode($dependence),
          'user_id' => Auth::user()->id
        ]);

        DB::commit(); //commit de la transaccion

        if ($dependence) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $dependence->state
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
      return (new DependenceExport)->download('Dependences.csv', \Maatwebsite\Excel\Excel::CSV);

      //return Excel::download(new TypeDocumentExport, 'TypeDocuments.csv');
    }

    public function dataExport()
    {
      return Dependence::select('id as ID', 'identification as Identificación', 'names as Nombre', 'telephone as Telefono', 'address as Dirección', DB::raw("(CASE state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), DB::raw("(CASE type WHEN 'person' THEN 'Persona' ELSE 'Dependencia' END) AS Tipo"))
                      ->get();
    }
}
