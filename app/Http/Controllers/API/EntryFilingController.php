<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\CanceledEntryFiling;
use App\Company;
use App\EntryFiling;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\EntryFilingHasDependence;
use App\Exports\EntryFilingExport;
use App\UpFile;
use DateTime;
use PDF;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EntryFilingController extends Controller
{
  protected $entryFiling;
  /**
   * Create a new controller instance.
   *
   * @param instance $entryFiling instance
   *
   * @return void
   */
  public function __construct(EntryFiling $entryFiling)
  {
    $this->entryFiling = $entryFiling;
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      //retorna la información de la base de datos
      if ($request->fromDate && $request->toDate) { //retorna los datos que esten entre un rango de fechas especifico
        return EntryFiling::with(
          'upFiles',
          'dependences',
          'TypeDocument:id,name,days',
          'ContextType:id,name',
          'dependence:id,names',
          'Priority:id,name'
          )
          ->where('state', 1)
          ->whereBetween('created_at', [$request->fromDate, $request->toDate])
          ->get();
      }
      //retorna los datos que tengan fecha de almacenamiento hoy
      return EntryFiling::with(
        'upFiles',
        'dependences',
        'TypeDocument:id,name,days',
        'ContextType:id,name',
        'dependence:id,names',
        'Priority:id,name'
        )
        ->where('state', 1)
        ->get();
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
        $lastFiling = EntryFiling::where('year', $year)->get()->max('cons_year'); //buscamos el ultimo registro del año guardado en la abse de datos
        //aplicamos los ceros al valor para crear el radicado
        if (empty($lastFiling)) {
          $lastFiling = 0;
        }if ($lastFiling <= 9) {
          $lastFiling = "0000" . $lastFiling;
        }else if ($lastFiling >= 10 && $lastFiling <= 99) {
          $lastFiling = "000" . $lastFiling;
        }else if ($lastFiling >= 100 && $lastFiling <= 999) {
          $lastFiling = "00" . $lastFiling;
        }else if ($lastFiling >= 1000 && $lastFiling <= 9999) {
          $lastFiling = "0" . $lastFiling;
        }else {
          $lastFiling = $lastFiling;
        };
        $data = $request->all(); //capturamos los parametros que vienen en la petición
        $data['date'] = Carbon::now();
        $data['cons_year'] = $lastFiling + 1; //se suma 1 para aumentar el consecutivo del año
        $data['year'] = date("Y");
        $data['slug'] = Str::slug($request->title,'-');
        $data['settled'] = date("Y") . date("m") . date("d") . 1 . $lastFiling + 1; //se agrega el numero 1 para identificar el typo de radicado y se suma 1 para aumentar el consecutivo del año
        $data['state'] = 1;
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $entryFiling = EntryFiling::create($data); //Guarda la informacion del nuevo registro

        $dependences = $request->dependences;//se recibe lo que se tiene en la propiedad data array dataOrden
        //recorro todos los elementos y los almaceno (todos los destinatarios del radicado)
        foreach ($dependences as $key => $det) {
          $info = new EntryFilingHasDependence();
          $info->entry_filing_id = $entryFiling->id;
          $info->dependence_id = $det['id'];
          $info->save();//guarda la información
        }
        DB::commit(); //commit de la transaccion

        if ($entryFiling) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $entryFiling
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
      //retorna la información especifica por ID de la base de datos
      return EntryFiling::with(
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
        $entry_filing = EntryFiling::find($id);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'entry_filings',
          'action' => 'update',
          'data_id' => $entry_filing->id,
          'entry_filing_id' => $entry_filing->id,
          'all_data' => json_encode($entry_filing),
          'user_id' => Auth::user()->id
        ]);

        //captura los parametros q vienen en la petición
        $entry_filing->title = $request->title;
        /* $entry_filing->settled = $request->settled; */
        $entry_filing->slug = Str::slug($request->title,'-');
        $entry_filing->access_level = $request->access_level;
        $entry_filing->means_document = $request->means_document;
        $entry_filing->state = $request->state;
        $entry_filing->folios = $request->folios;
        $entry_filing->subject = $request->subject;
        $entry_filing->key_words = $request->key_words;
        $entry_filing->attachments = $request->attachments;
        $entry_filing->priority_id = $request->priority_id;
        $entry_filing->type_document_id = $request->type_document_id;
        $entry_filing->context_type_id = $request->context_type_id;
        $entry_filing->save(); //guarda el resgitro actualizado
        DB::commit(); //commit de la transaccion

        if ($entry_filing) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $entry_filing
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

    public function cancelFiling(Request $request, $id)
    {
      //anular radicado, no elimina solo cambia el estado
      try {
        DB::beginTransaction();

        $entry_filing = EntryFiling::find($id); //Busca registro por ID

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'entry_filings',
          'action' => 'canceled',
          'data_id' => $entry_filing->id,
          'entry_filing_id' => $entry_filing->id,
          'all_data' => json_encode($entry_filing),
          'user_id' => Auth::user()->id
        ]);

        //Add data in cancels_entry_filings
        $cancelFiling = CanceledEntryFiling::create([
          'description' => $request->description,
          'cancellationReason_id' => $request->cancellationReason_id,
          'entryFiling_id' => $request->entryFiling_id,
          'user_id' => Auth::user()->id
        ]);

        $entry_filing->state = 2; //cambia el estado del radicado
        $entry_filing->save(); //guarda el estado

        DB::commit(); //commit de la transaccion

        if ($entry_filing) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Anulado con éxito',
            'data' => $entry_filing->state
          ], 202);
        }else{ //respuesta de error
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
      if($request->hasFile('file')) {
        //capturamos name del file
        $lastAnnexe = $request->annexes + 1;
        $fileName = $request->settled .'-'.  $lastAnnexe . '.' . $request->file->getClientOriginalExtension();
        //si no existe, creamos la Ruta para guardar los archivos
        $path =  "uploads/entryFiling/$request->settled";
        if (!file_exists("storage/$path")) {
          File::makeDirectory("storage/$path", 0777, true, true);
        }
        //Mueve el archivo a la ruta especifica
        $pathFull = Storage::disk('public')->putFileAs(
          $path , $request->file , $fileName
        );

        $entryFiling = EntryFiling::find($request->id);
        if ($entryFiling) {
          //actualizar total de anexos
          $entryFiling->annexes = $lastAnnexe;
          $entryFiling->save();

          //create File in DB
          $file = UpFile::create([
            'name' => $fileName,
            'url' => $pathFull,
            'fileable_type' => "App\\EntryFiling",
            'fileable_id' => $entryFiling->id,
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

        $selectedFile = UpFile::find($id);//Busca registro por ID

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
        DB::commit();//commit de la transaccion

        if ($selectedFile) { //respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Archivo eliminado con éxito',
            'data' => $selectedFile
          ], 202);
        }else{ //respuesta de error
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
        $selectedFile = UpFile::find($id);//buscamos el registro por ID
        $pathFull = Storage::disk('public')->get($selectedFile->url);//capturamo sla ruta del archivo
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
        $entryFiling = EntryFiling::with(
          'upFiles',
          'dependences',
          'TypeDocument:id,name',
          'ContextType:id,name',
          'dependence:id,names',
          'Priority:id,name'
          )
          ->where('state', 1)
          ->whereBetween('created_at', [$request->fromDate, $request->toDate])
          ->get();
      }else{ //captura la información de hoy
        $entryFiling = EntryFiling::with(
          'upFiles',
          'dependences',
          'TypeDocument:id,name',
          'ContextType:id,name',
          'dependence:id,names',
          'Priority:id,name'
          )
          ->where('state', 1)
          ->whereDate('created_at', now())
          ->get();
      }
      //return $entryFiling;
      // share data to view
      view()->share('entryFiling', $entryFiling);
      $date = Carbon::now();
      $fromDate = ($request->fromDate) ? (DateTime::createFromFormat('Y-m-d H:i:s', $request->fromDate)->format('Y-m-d h:i a')) : '';
      $toDate = ($request->toDate) ? (DateTime::createFromFormat('Y-m-d H:i:s', $request->toDate)->format('Y-m-d h:i a')) : '';
      $company = Company::get();
      $rangeDate = ($request->fromDate && $request->toDate) ? $fromDate .' a '. $toDate : $date->isoFormat('DD/MM/YYYY');

      //crea el pdf con los parametros
      $pdf = PDF::loadView('Pdf.templateEntryFiling', compact(
        'entryFiling', 'company', 'rangeDate' , 'fromDate', 'toDate'
      ))->setPaper('a4', 'landscape');
      //$pdf->set_option('isPhpEnabled', true);
      return $pdf->stream('Planilla.pdf'); //muestra el pdf
    }

    //download XLSX
    public function export(Request $request)
    {
      if ($request->fromDate && $request->toDate) {
        return EntryFiling::Join ('dependences as rem', 'entry_filings.dependence_id', '=', 'rem.id')
                        ->join('entry_filing_has_dependences', 'entry_filing_has_dependences.entry_filing_id', '=', 'entry_filings.id')
                        ->join('dependences', 'dependences.id', '=', 'entry_filing_has_dependences.dependence_id')
                        ->join('type_documents', 'entry_filings.type_document_id', '=', 'type_documents.id')
                        ->select('entry_filings.id as ID', 'entry_filings.settled as Radicado', 'entry_filings.created_at as Fecha', DB::raw("(CASE entry_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'entry_filings.title as Titulo', 'entry_filings.subject as Asunto', 'entry_filings.folios as Folios', 'entry_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE entry_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                        ->whereBetween('entry_filings.created_at', [$request->fromDate, $request->toDate])
                        ->where('entry_filings.state', 1)
                        ->get();
      } else {
        return EntryFiling::Join ('dependences as rem', 'entry_filings.dependence_id', '=', 'rem.id')
                        ->join('entry_filing_has_dependences', 'entry_filing_has_dependences.entry_filing_id', '=', 'entry_filings.id')
                        ->join('dependences', 'dependences.id', '=', 'entry_filing_has_dependences.dependence_id')
                        ->join('type_documents', 'entry_filings.type_document_id', '=', 'type_documents.id')
                        ->select('entry_filings.id as ID', 'entry_filings.settled as Radicado', 'entry_filings.created_at as Fecha', DB::raw("(CASE entry_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'entry_filings.title as Titulo', 'entry_filings.subject as Asunto', 'entry_filings.folios as Folios', 'entry_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE entry_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                        /* ->whereDate('entry_filings.created_at', now()) */
                        ->where('entry_filings.state', 1)
                        ->get();
      }
    }
}
