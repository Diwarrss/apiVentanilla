<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\InternalFiling;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use File;
use Storage;
use App\Audit;
use App\CanceledInternalFiling;
use App\UpFile;
use DateTime;
use PDF;
use Illuminate\Support\Facades\Auth;
use App\Company;
use App\InternalFilingHasDependence;

class InternalFilingController extends Controller
{
  protected $internalFiling;
  /**
   * Create a new controller instance.
   *
   * @param instance $internalFiling instance
   *
   * @return void
   */
  public function __construct(InternalFiling $internalFiling)
  {
    $this->internalFiling = $internalFiling;
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    //retorna la información de la base de datos
    $user = Auth::user()->dependencePerson_id;
    if ($request->type == 1) {
      if ($request->fromDate && $request->toDate) { //retorna los datos que esten entre un rango de fechas especifico
        return InternalFiling::with(
          'upFiles',
          'dependences',
          'TypeDocument:id,name,days',
          'ContextType:id,name',
          'dependence:id,names',
          'Priority:id,name'
        )
          ->where('state', '!=', 2)
          ->whereHas('dependences', function($query) use ($user)  {//condicion en la relación
            //retorna los radicados que tienen como destinatario al usuario a a la dependencia a la que pertenece
            $query->where('internal_filing_has_dependences.dependence_id', $user);
          })
          ->whereBetween('created_at', [$request->fromDate, $request->toDate])
          ->get();
      }
      //retorna los datos que tengan fecha de almacenamiento hoy
      return InternalFiling::with(
        'upFiles',
        'dependences',
        'TypeDocument:id,name,days',
        'ContextType:id,name',
        'dependence:id,names',
        'Priority:id,name'
      )
        ->where('state', '!=', 2)
        ->whereHas('dependences', function($query) use ($user)  {//condicion en la relación
          //retorna los radicados que tienen como destinatario al usuario a a la dependencia a la que pertenece
          $query->where('internal_filing_has_dependences.dependence_id', $user);
        })
        ->get();
    } else if ($request->type == 2) {
      if ($request->fromDate && $request->toDate) { //retorna los datos que esten entre un rango de fechas especifico
        return InternalFiling::with(
          'upFiles',
          'dependences',
          'TypeDocument:id,name,days',
          'ContextType:id,name',
          'dependence:id,names',
          'Priority:id,name'
        )
          ->where('state', '!=', 2)
          ->where('dependence_id', $user)
          ->whereBetween('created_at', [$request->fromDate, $request->toDate])
          ->get();
      }
      //retorna los datos que tengan fecha de almacenamiento hoy
      return InternalFiling::with(
        'upFiles',
        'dependences',
        'TypeDocument:id,name,days',
        'ContextType:id,name',
        'dependence:id,names',
        'Priority:id,name'
      )
        ->where('state', '!=', 2)
        ->where('dependence_id', $user)
        ->get();
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //Guarda la informacion del nuevo registro
    try {
      DB::beginTransaction();

      $year = date("Y"); //capturamos el año actual
      $lastFiling = InternalFiling::where('year', $year)->get()->max('cons_year'); //buscamos el ultimo registro del año guardado en la abse de datos
      //aplicamos los ceros al valor para crear el radicado
      if (empty($lastFiling)) {
        $lastFiling = 0;
      }
      if ($lastFiling <= 9) {
        $lastFiling = "0000" . $lastFiling;
      } else if ($lastFiling >= 10 && $lastFiling <= 99) {
        $lastFiling = "000" . $lastFiling;
      } else if ($lastFiling >= 100 && $lastFiling <= 999) {
        $lastFiling = "00" . $lastFiling;
      } else if ($lastFiling >= 1000 && $lastFiling <= 9999) {
        $lastFiling = "0" . $lastFiling;
      } else {
        $lastFiling = $lastFiling;
      };
      $data = $request->all(); //capturamos los parametros que vienen en la petición
      $data['date'] = Carbon::now();
      $data['cons_year'] = $lastFiling + 1; //se suma 1 para aumentar el consecutivo del año
      $data['year'] = date("Y");
      $data['slug'] = Str::slug($request->title, '-');
      $data['settled'] = date("Y") . date("m") . date("d") . 3 . $lastFiling + 1; //se agrega el numero 1 para identificar el typo de radicado y se suma 1 para aumentar el consecutivo del año
      $data['state'] = 1;
      $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
      $internalFiling = InternalFiling::create($data); //Guarda la informacion del nuevo registro

      $dependences = $request->dependences; //se recibe lo que se tiene en la propiedad data array dataOrden
      //recorro todos los elementos y los almaceno (todos los destinatarios del radicado)
      foreach ($dependences as $key => $det) {
        $info = new InternalFilingHasDependence();
        $info->internal_filing_id = $internalFiling->id;
        $info->dependence_id = $det['id'];
        $info->save(); //guarda la información
      }
      DB::commit(); //commit de la transaccion

      if ($internalFiling) { //respuesta exitosa
        return response()->json([
          'type' => 'success',
          'message' => 'Creado con éxito',
          'data' => $internalFiling
        ], 201);
      } else { //respuesta de error
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
    //retorna la información especifica por ID de la base de datos
    return InternalFiling::with(
      'upFiles',
      'dependences',
      'TypeDocument:id,name,days',
      'ContextType:id,name',
      'dependence:id,names',
      'Priority:id,name'
    )->find($id);
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
    //actualiza la infomracion del registro especifico
    try {
      DB::beginTransaction();

      //Busca registro por ID
      $internal_filing = InternalFiling::find($id);

      //Add data in table audits
      $audit = Audit::create([
        'table' => 'internal_filings',
        'action' => 'update',
        'data_id' => $internal_filing->id,
        'internal_filing_id' => $internal_filing->id,
        'all_data' => json_encode($internal_filing),
        'user_id' => Auth::user()->id
      ]);

      //captura los parametros q vienen en la petición
      $internal_filing->title = $request->title;
      /* $internal_filing->settled = $request->settled; */
      $internal_filing->slug = Str::slug($request->title, '-');
      $internal_filing->access_level = $request->access_level;
      $internal_filing->means_document = $request->means_document;
      $internal_filing->state = $request->state;
      $internal_filing->folios = $request->folios;
      $internal_filing->subject = $request->subject;
      $internal_filing->key_words = $request->key_words;
      $internal_filing->attachments = $request->attachments;
      $internal_filing->priority_id = $request->priority_id;
      $internal_filing->type_document_id = $request->type_document_id;
      $internal_filing->context_type_id = $request->context_type_id;
      $internal_filing->save(); //guarda el resgitro actualizado
      DB::commit(); //commit de la transaccion

      if ($internal_filing) { //respuesta exitosa
        return response()->json([
          'type' => 'success',
          'message' => 'Actualizado con éxito',
          'data' => $internal_filing
        ], 202);
      } else { //respuesta de error
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

  public function cancelFiling(Request $request, $id)
  {
    //anular radicado, no elimina solo cambia el estado
    try {
      DB::beginTransaction();

      $internal_filing = InternalFiling::find($id); //Busca registro por ID

      //Add data in table audits
      $audit = Audit::create([
        'table' => 'internal_filings',
        'action' => 'canceled',
        'data_id' => $internal_filing->id,
        'internal_filing_id' => $internal_filing->id,
        'all_data' => json_encode($internal_filing),
        'user_id' => Auth::user()->id
      ]);

      //Add data in cancels_internal_filings
      $cancelFiling = CanceledInternalFiling::create([
        'description' => $request->description,
        'cancellationReason_id' => $request->cancellationReason_id,
        'internalFiling_id' => $request->internalFiling_id,
        'user_id' => Auth::user()->id
      ]);

      $internal_filing->state = 2; //cambia el estado del radicado
      $internal_filing->save(); //guarda el estado

      DB::commit(); //commit de la transaccion

      if ($internal_filing) { //respuesta exitosa
        return response()->json([
          'type' => 'success',
          'message' => 'Anulado con éxito',
          'data' => $internal_filing->state
        ], 202);
      } else { //respuesta de error
        return response()->json([
          'type' => 'error',
          'message' => 'Error al anular',
          'data' => []
        ], 204);
      }
    } catch (Exception $e) { //error en el proceso
      return response()->json([
        'type' => 'error',
        'message' => 'Error al anular',
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

  //upload files temp
  public function uploadTempFiles(Request $request)
  {
    /* if($request->file()) {
        //$public_path = public_path();
        $fileName = $request->file->getClientOriginalName();
        $filePath = $request->file('file')->storeAs('temps', $fileName, 'public');
        if (!file_exists($filePath)) {
          File::makeDirectory($filePath, 0777, true, true);
        }
        return response()->json([
          'type' => 'success',
          'message' => 'Archivo subido con éxito',
          'data' => $filePath
        ], 200);
      } */
    //carga el archivo al storage
    if ($request->hasFile('file')) {
      //capturamos name del file
      $lastAnnexe = $request->annexes + 1;
      $fileName = $request->settled . '-' .  $lastAnnexe . '.' . $request->file->getClientOriginalExtension();
      //si no existe, creamos la Ruta para guardar los archivos
      $path =  "uploads/internalFiling/$request->settled";
      if (!file_exists("storage/$path")) {
        File::makeDirectory("storage/$path", 0777, true, true);
      }
      //Mueve el archivo a la ruta especifica
      $pathFull = Storage::disk('public')->putFileAs(
        $path,
        $request->file,
        $fileName
      );

      $internalFiling = InternalFiling::find($request->id);
      if ($internalFiling) {
        //actualizar total de anexos
        $internalFiling->annexes = $lastAnnexe;
        $internalFiling->save();

        //create File in DB
        $file = UpFile::create([
          'name' => $fileName,
          'url' => $pathFull,
          'fileable_type' => "App\\InternalFiling",
          'fileable_id' => $internalFiling->id,
          'type' => 0
        ]);

        //creamos registro en auditoria
        $audit = Audit::create([
          'table' => 'up_files',
          'action' => 'uploadFile',
          'data_id' => $file->id,
          'up_file_id' => $file->id,
          'all_data' => json_encode($file),
          'user_id' => Auth::user()->id
        ]);
      }
      return response()->json([ //respuesta exitosa
        'type' => 'success',
        'message' => 'Archivo subido con éxito',
        'data' => $file
      ], 200);
    }
  }

  public function deleteFile(Request $request, $id)
  {
    //eliminar archivo (storage y DB)
    try {
      DB::beginTransaction();

      $selectedFile = UpFile::find($id); //Busca registro por ID

      //borramos archivo del storage
      $deletePath = Storage::disk('public')->delete($selectedFile->url);

      //creamos registro en auditoria
      $audit = Audit::create([
        'table' => 'up_files',
        'action' => 'deleteFile',
        'data_id' => $selectedFile->id,
        'up_file_id' => $selectedFile->id,
        'all_data' => json_encode($selectedFile),
        'user_id' => Auth::user()->id
      ]);

      //borramos dato de up_files
      $selectedFile->delete();
      DB::commit(); //commit de la transaccion

      if ($selectedFile) { //respuesta exitosa
        return response()->json([
          'type' => 'success',
          'message' => 'Archivo eliminado con éxito',
          'data' => $selectedFile
        ], 202);
      } else { //respuesta de error
        return response()->json([
          'type' => 'error',
          'message' => 'Error al eliminar',
          'data' => []
        ], 204);
      }
    } catch (Exception $e) { //error en el proceso
      return response()->json([
        'type' => 'error',
        'message' => 'Error al eliminar',
        'data' => []
      ], 204);
      DB::rollBack(); //si hay error no ejecute la transaccion
    }
  }

  public function downloadFile(Request $request, $id)
  {
    //descarga el archivo en el equipo local
    try {
      DB::beginTransaction();
      $selectedFile = UpFile::find($id); //buscamos el registro por ID
      $pathFull = Storage::disk('public')->get($selectedFile->url); //capturamo sla ruta del archivo
      return response()->download($pathFull); //descargamos el archivo con una ruta especifica
    } catch (Exception $e) { //respuesta exitosa
      return response()->json([
        'type' => 'error',
        'message' => 'Error al descargar',
        'data' => []
      ], 204);
      DB::rollBack(); //si hay error no ejecute la transaccion
    }
  }

  public function generateTemplate(Request $request)
  {
    //generar planilla de radicados por fecha o los de hoy
    if ($request->fromDate && $request->toDate) { //captura la información entre dos fechas especificas
      $internalFiling = InternalFiling::with(
        'upFiles',
        'dependences',
        'TypeDocument:id,name',
        'ContextType:id,name',
        'dependence:id,names',
        'Priority:id,name'
      )
        ->where('state', '!=', 2)
        ->whereBetween('created_at', [$request->fromDate, $request->toDate])
        ->get();
    } else { //captura la información de hoy
      $internalFiling = InternalFiling::with(
        'upFiles',
        'dependences',
        'TypeDocument:id,name',
        'ContextType:id,name',
        'dependence:id,names',
        'Priority:id,name'
      )
        ->where('state', '!=', 2)
        ->whereDate('created_at', now())
        ->get();
    }
    //return $internalFiling;
    // share data to view
    view()->share('internalFiling', $internalFiling);
    $date = Carbon::now();
    $fromDate = ($request->fromDate) ? (DateTime::createFromFormat('Y-m-d H:i:s', $request->fromDate)->format('Y-m-d h:i a')) : '';
    $toDate = ($request->toDate) ? (DateTime::createFromFormat('Y-m-d H:i:s', $request->toDate)->format('Y-m-d h:i a')) : '';
    $company = Company::get();
    $rangeDate = ($request->fromDate && $request->toDate) ? $fromDate . ' a ' . $toDate : $date->isoFormat('DD/MM/YYYY');

    //crea el pdf con los parametros
    $pdf = PDF::loadView('Pdf.templateinternalFiling', compact(
      'internalFiling',
      'company',
      'rangeDate',
      'fromDate',
      'toDate'
    ))->setPaper('a4', 'landscape');
    //$pdf->set_option('isPhpEnabled', true);
    return $pdf->stream('Planilla.pdf'); //muestra el pdf
  }

  //download XLSX
  public function export(Request $request)
  {
    if ($request->fromDate && $request->toDate) {
      return InternalFiling::Join('dependences as rem', 'internal_filings.dependence_id', '=', 'rem.id')
        ->join('internal_filing_has_dependences', 'internal_filing_has_dependences.internal_filing_id', '=', 'internal_filings.id')
        ->join('dependences', 'dependences.id', '=', 'internal_filing_has_dependences.dependence_id')
        ->join('type_documents', 'internal_filings.type_document_id', '=', 'type_documents.id')
        ->select('internal_filings.id as ID', 'internal_filings.settled as Radicado', 'internal_filings.created_at as Fecha', DB::raw("(CASE internal_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'internal_filings.title as Titulo', 'internal_filings.subject as Asunto', 'internal_filings.folios as Folios', 'internal_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE internal_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
        ->whereBetween('internal_filings.created_at', [$request->fromDate, $request->toDate])
        ->where('internal_filings.state', '!=', 2)
        ->get();
    } else {
      return InternalFiling::Join('dependences as rem', 'internal_filings.dependence_id', '=', 'rem.id')
        ->join('internal_filing_has_dependences', 'internal_filing_has_dependences.internal_filing_id', '=', 'internal_filings.id')
        ->join('dependences', 'dependences.id', '=', 'internal_filing_has_dependences.dependence_id')
        ->join('type_documents', 'internal_filings.type_document_id', '=', 'type_documents.id')
        ->select('internal_filings.id as ID', 'internal_filings.settled as Radicado', 'internal_filings.created_at as Fecha', DB::raw("(CASE internal_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'internal_filings.title as Titulo', 'internal_filings.subject as Asunto', 'internal_filings.folios as Folios', 'internal_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE internal_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
        /* ->whereDate('internal_filings.created_at', now()) */
        ->where('internal_filings.state', '!=', 2)
        ->get();
    }
  }
}
