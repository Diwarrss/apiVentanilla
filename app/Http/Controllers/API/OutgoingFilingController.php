<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\canceledOutgoingFiling;
use App\Company;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\OutgoingFiling;
use App\OutgoingFilingHasDependence;
use App\UpFile;
use DateTime;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use File;
use Illuminate\Support\Facades\Storage;

class OutGoingFilingController extends Controller
{
  protected $outGoingFiling;
  /**
   * Create a new controller instance.
   *
   * @param instance $outGoingFiling instance
   *
   * @return void
   */
  public function __construct(outGoingFiling $outGoingFiling)
  {
    $this->outGoingFiling = $outGoingFiling;
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {//retorna la información de la base de datos
      if ($request->fromDate && $request->toDate) {//retorna los datos que esten entre un rango de fechas especifico
        return OutgoingFiling::with(
          'upFiles',
          'dependence',
          'TypeDocument:id,name',
          'ContextType:id,name',
          'dependence:id,names',
          'Priority:id,name'
          )
          ->where('state', 1)
          ->whereBetween('created_at', [$request->fromDate, $request->toDate])
          /* ->whereBetween('created_at', [$request->fromDate." 00:00:00", $request->toDate." 23:59:59"]) */
          ->get();
      }
      //retorna los datos que tengan fecha de almacenamiento hoy
      return OutgoingFiling::with(
        'upFiles',
        'dependence',
        'TypeDocument:id,name',
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
    {//Guarda la informacion del nuevo registro
      try {
        DB::beginTransaction();

        //we search
        $year = date("Y"); //capturamos el año actual
        $lastFiling = OutgoingFiling::where('year', $year)->get()->max('cons_year');//buscamos el ultimo registro del año guardado en la abse de datos
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
        $data['settled'] = date("Y") . date("m") . date("d") . 2 . $lastFiling + 1;//se agrega el numero 1 para identificar el typo de radicado y se suma 1 para aumentar el consecutivo del año
        $data['state'] = 1;
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $outgoingFiling = OutgoingFiling::create($data);//Guarda la informacion del nuevo registro

        $dependence = $request->dependence;//se recibe lo que se tiene en la propiedad data array dataOrden
        //recorro todos los elementos y los almaceno (todos los destinatarios del radicado)
        foreach ($dependence as $key => $det) {
          $info = new OutgoingFilingHasDependence();
          $info->outgoing_filing_id = $outgoingFiling->id;
          $info->dependence_id = $det['id'];
          $info->save();//guarda la información
        }
        DB::commit(); //commit de la transaccion

        if ($outgoingFiling) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $outgoingFiling
          ], 201);
        }else{//error en el proceso
          return response()->json([
            'type' => 'error',
            'message' => 'Error al guardar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {//si hay error no ejecute la transaccion
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
    {//retorna la información especifica por ID de la base de datos
      return OutgoingFiling::with(
        'upFiles',
        'dependence',
        'TypeDocument:id,name',
        'ContextType:id,name',
        'dependences:id,names',
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
    {//actualiza la infomracion del registro especifico
      try {
        DB::beginTransaction();

        //Busca registro por ID
        $outgoing_filing = OutgoingFiling::find($id);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'outgoing_filings',
          'action' => 'update',
          'data_id' => $outgoing_filing->id,
          'outgoing_filing_id' => $outgoing_filing->id,
          'all_data' => json_encode($outgoing_filing),
          'user_id' => Auth::user()->id
        ]);

        //captura los parametros q vienen en la petición
        $outgoing_filing->title = $request->title;
        /* $outgoing_filing->settled = $request->settled; */
        $outgoing_filing->slug = Str::slug($request->title,'-');
        $outgoing_filing->access_level = $request->access_level;
        $outgoing_filing->means_document = $request->means_document;
        $outgoing_filing->state = $request->state;
        $outgoing_filing->folios = $request->folios;
        $outgoing_filing->subject = $request->subject;
        $outgoing_filing->key_words = $request->key_words;
        $outgoing_filing->attachments = $request->attachments;
        $outgoing_filing->priority_id = $request->priority_id;
        $outgoing_filing->type_document_id = $request->type_document_id;
        $outgoing_filing->context_type_id = $request->context_type_id;
        $outgoing_filing->save();//guarda el resgitro actualizado
        DB::commit(); //commit de la transaccion

        if ($outgoing_filing) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $outgoing_filing
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

    public function cancelFiling(Request $request, $id)
    {//anular radicado, no elimina solo cambia el estado
      try {
        DB::beginTransaction();

        $outgoing_filing = OutgoingFiling::find($id);//Busca registro por ID

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'outgoing_filings',
          'action' => 'canceled',
          'data_id' => $outgoing_filing->id,
          'outgoing_filing_id' => $outgoing_filing->id,
          'all_data' => json_encode($outgoing_filing),
          'user_id' => Auth::user()->id
        ]);

        //Add data in cancels_entry_filings
        $cancelFiling = canceledOutgoingFiling::create([
          'description' => $request->description,
          'cancellationReason_id' => $request->cancellationReason_id,
          'outgoingFiling_id' => $request->outgoingFiling_id,
          'user_id' => Auth::user()->id
        ]);

        $outgoing_filing->state = !$outgoing_filing->state;//cambia el estado del radicado
        $outgoing_filing->save();//guarda el estado

        DB::commit(); //commit de la transaccion

        if ($outgoing_filing) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Anulado con éxito',
            'data' => $outgoing_filing->state
          ], 202);
        }else{//respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al anular',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {//error en el proceso
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
        $path =  "uploads/outgoingFiling/$request->settled";
        if (!file_exists("storage/$path")) {
          File::makeDirectory("storage/$path", 0777, true, true);
        }
        //Mueve el archivo a la ruta especifica
        $pathFull = Storage::disk('public')->putFileAs(
          $path , $request->file , $fileName
        );

        $outgoingFiling = OutgoingFiling::find($request->id);
        if ($outgoingFiling) {
          //actualizar total de anexos
          $outgoingFiling->annexes = $lastAnnexe;
          $outgoingFiling->save();

          //create File in DB
          $file = UpFile::create([
            'name' => $fileName,
            'url' => $pathFull,
            'fileable_type' => "App\\outgoingFiling",
            'fileable_id' => $outgoingFiling->id,
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
        return response()->json([//respuesta exitosa
          'type' => 'success',
          'message' => 'Archivo subido con éxito',
          'data' => $file
        ], 200);
      }
    }

    public function deleteFile(Request $request, $id)
    {//eliminar archivo (storage y DB)
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

        if ($selectedFile) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Archivo eliminado con éxito',
            'data' => $selectedFile
          ], 202);
        }else{//respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al eliminar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {//error en el proceso
        return response()->json([
          'type' => 'error',
          'message' => 'Error al eliminar',
          'data' => []
        ], 204);
        DB::rollBack(); //si hay error no ejecute la transaccion
      }
    }

    public function downloadFile(Request $request, $id)
    {//descarga el archivo en el equipo local
      try {
        //return $request;
        DB::beginTransaction();
        $selectedFile = UpFile::find($id);//buscamos el registro por ID
        $pathFull = Storage::disk('public')->get($selectedFile->url);//capturamo sla ruta del archivo
        return response()->download($pathFull);//descargamos el archivo con una ruta especifica
      } catch (Exception $e) {//respuesta exitosa
        return response()->json([
          'type' => 'error',
          'message' => 'Error al descargar',
          'data' => []
        ], 204);
        DB::rollBack(); //si hay error no ejecute la transaccion
      }
    }

    public function uploadTempFilesGuide(Request $request)
    {
      //carga el archivo al storage
      if($request->hasFile('file')) {
        //capturamos name del file
        $fileName = $request->settled .'-Guide.' . $request->file->getClientOriginalExtension();
        //si no existe, creamos la Ruta para guardar los archivos
        $path =  "uploads/outgoingFiling/$request->settled";
        if (!file_exists("storage/$path")) {
          File::makeDirectory("storage/$path", 0777, true, true);
        }
        //Mueve el archivo a la ruta especifica
        $pathFull = Storage::disk('public')->putFileAs(
          $path , $request->file , $fileName
        );

        $outgoingFiling = OutgoingFiling::find($request->id);
        if ($outgoingFiling) {
          //create File in DB
          $file = UpFile::create([
            'name' => $fileName,
            'url' => $pathFull,
            'fileable_type' => "App\\outgoingFiling",
            'fileable_id' => $outgoingFiling->id,
            'type' => 1
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

          /* DB::table('up_files')->insert([
            'url' => $pathFull,
            'fileable_type' => "App\\outgoingFiling",
            'fileable_id' => $outgoingFiling->id,
            'created_at' => now(),
            'updated_at' => now()
          ]); */
        }
        return response()->json([//respuesta exitosa
          'type' => 'success',
          'message' => 'Archivo subido con éxito',
          'data' => $file
        ], 200);
      }
    }

    public function generateTemplate(Request $request)
    {//generar planilla de radicados por fecha o los de hoy
      if ($request->fromDate && $request->toDate) {//captura la información entre dos fechas especificas
        $outgoingFiling = OutgoingFiling::with(
          'upFiles',
          'dependence',
          'TypeDocument:id,name',
          'ContextType:id,name',
          'dependences:id,names',
          'Priority:id,name'
          )
          ->where('state', 1)
          ->whereBetween('created_at', [$request->fromDate, $request->toDate])
          ->get();
      }else{//captura la información de hoy
        $outgoingFiling = OutgoingFiling::with(
          'upFiles',
          'dependence',
          'TypeDocument:id,name',
          'ContextType:id,name',
          'dependences:id,names',
          'Priority:id,name'
          )
          ->where('state', 1)
          ->whereDate('created_at', now())
          ->get();
      }
      // share data to view
      view()->share('outgoingFiling', $outgoingFiling);
      $date = Carbon::now();
      $fromDate = ($request->fromDate) ? (DateTime::createFromFormat('Y-m-d H:i:s', $request->fromDate)->format('Y-m-d h:i a')) : '';
      $toDate = ($request->toDate) ? (DateTime::createFromFormat('Y-m-d H:i:s', $request->toDate)->format('Y-m-d h:i a')) : '';
      $company = Company::get();
      $rangeDate = ($request->fromDate && $request->toDate) ? $fromDate .' a '. $toDate : $date->isoFormat('DD/MM/YYYY');

      //crea el pdf con los parametros
      $pdf = PDF::loadView('Pdf.templateOutgoingFiling', compact(
        'outgoingFiling', 'company', 'rangeDate' , 'fromDate', 'toDate'
      ))->setPaper('a4', 'landscape');
      //$pdf->set_option('isPhpEnabled', true);
      return $pdf->stream('Planilla.pdf');//muestra el pdf
    }

    //download csv
    public function export(Request $request)
    {
      if ($request->fromDate && $request->toDate) {
        return OutgoingFiling::Join ('dependences', 'outgoing_filings.dependence_id', '=', 'dependences.id')
                        ->join('outgoing_filing_has_dependences', 'outgoing_filing_has_dependences.outgoing_filing_id', '=', 'outgoing_filings.id')
                        ->join('dependence', 'dependence.id', '=', 'outgoing_filing_has_dependences.dependence_id')
                        ->join('type_documents', 'outgoing_filings.type_document_id', '=', 'type_documents.id')
                        ->select('outgoing_filings.id as ID', 'outgoing_filings.settled as Radicado', 'outgoing_filings.created_at as Fecha', DB::raw("(CASE outgoing_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'outgoing_filings.title as Titulo', 'outgoing_filings.subject as Asunto', 'outgoing_filings.folios as Folios', 'outgoing_filings.annexes as Anexos', 'dependences.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE outgoing_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                        ->whereBetween('outgoing_filings.created_at', [$request->fromDate, $request->toDate])
                        ->where('outgoing_filings.state', 1)
                        ->get();
      } else {
        return OutgoingFiling::Join ('dependences', 'outgoing_filings.dependence_id', '=', 'dependences.id')
                        ->join('outgoing_filing_has_dependences', 'outgoing_filing_has_dependences.outgoing_filing_id', '=', 'outgoing_filings.id')
                        ->join('dependences', 'dependences.id', '=', 'outgoing_filing_has_dependences.dependence_id')
                        ->join('type_documents', 'outgoing_filings.type_document_id', '=', 'type_documents.id')
                        ->select('outgoing_filings.id as ID', 'outgoing_filings.settled as Radicado', 'outgoing_filings.created_at as Fecha', DB::raw("(CASE outgoing_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'outgoing_filings.title as Titulo', 'outgoing_filings.subject as Asunto', 'outgoing_filings.folios as Folios', 'outgoing_filings.annexes as Anexos', 'dependences.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE outgoing_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                        ->whereDate('outgoing_filings.created_at', now())
                        ->where('outgoing_filings.state', 1)
                        ->get();
      }
    }
}
