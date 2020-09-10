<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\canceledOutgoingFiling;
use App\Company;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\OutgoingFiling;
use App\OutgoingFilingHasPeople;
use App\People;
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
    {
      if ($request->fromDate && $request->toDate) {
        return OutgoingFiling::with(
          'upFiles',
          'people',
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
      return OutgoingFiling::with(
        'upFiles',
        'people',
        'TypeDocument:id,name',
        'ContextType:id,name',
        'dependence:id,names',
        'Priority:id,name'
        )
        ->where('state', 1)
        ->whereDate('created_at', now())
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
      try {
        DB::beginTransaction();

        //we search
        $year = date("Y");
        $lastFiling = OutgoingFiling::where('year', $year)->get()->max('cons_year');
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
        $data = $request->all();
        $data['date'] = Carbon::now();
        $data['cons_year'] = $lastFiling + 1;
        $data['year'] = date("Y");
        $data['slug'] = Str::slug($request->title,'-');
        $data['settled'] = date("Y") . date("m") . date("d") . 2 . $lastFiling + 1;
        $data['state'] = 1;
        $data['user_id'] = Auth::user()->id;/* trae el usuario q esta autenticado */
        $outgoingFiling = OutgoingFiling::create($data);

        $people = $request->people;//se recibe lo que se tiene en la propiedad data array dataOrden
        //recorro todos los elementos
        foreach ($people as $key => $det) {
          $info = new OutgoingFilingHasPeople();
          $info->outgoing_filing_id = $outgoingFiling->id;
          $info->people_id = $det['id'];
          $info->save();
        }
        DB::commit(); //commit de la transaccion

        //get People for id
        //$people = People::find($data['people_id])

        if ($outgoingFiling) {
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $outgoingFiling
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
      return OutgoingFiling::with(
        'upFiles',
        'dependence',
        'TypeDocument:id,name',
        'ContextType:id,name',
        'People:id,names',
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
      try {
        DB::beginTransaction();

        /* $data = $request->all(); */
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
        $outgoing_filing->save();
        DB::commit(); //commit de la transaccion

        if ($outgoing_filing) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $outgoing_filing
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

    public function cancelFiling(Request $request, $id)
    {
      try {
        DB::beginTransaction();

        /* $data = $request->all(); */
        $outgoing_filing = OutgoingFiling::find($id);

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

        $outgoing_filing->state = !$outgoing_filing->state;
        $outgoing_filing->save();

        DB::commit(); //commit de la transaccion

        if ($outgoing_filing) {
          return response()->json([
            'type' => 'success',
            'message' => 'Anulado con éxito',
            'data' => $outgoing_filing->state
          ], 202);
        }else{
          return response()->json([
            'type' => 'error',
            'message' => 'Error al anular',
            'data' => []
          ], 204);
        }

      } catch (Exception $e) {
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

          /* DB::table('up_files')->insert([
            'url' => $pathFull,
            'fileable_type' => "App\\outgoingFiling",
            'fileable_id' => $outgoingFiling->id,
            'created_at' => now(),
            'updated_at' => now()
          ]); */
        }
        return response()->json([
          'type' => 'success',
          'message' => 'Archivo subido con éxito',
          'data' => $file
        ], 200);
      }
    }

    public function deleteFile(Request $request, $id)
    {
      try {
        DB::beginTransaction();
        //$selectedFile = UpFile::where(['id', $id]);
        $selectedFile = UpFile::find($id);

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
        DB::commit();

        if ($selectedFile) {
          return response()->json([
            'type' => 'success',
            'message' => 'Archivo eliminado con éxito',
            'data' => $selectedFile
          ], 202);
        }else{
          return response()->json([
            'type' => 'error',
            'message' => 'Error al eliminar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {
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
      try {
        //return $request;
        DB::beginTransaction();
        $selectedFile = UpFile::find($id);
        $pathFull = Storage::disk('public')->get($selectedFile->url);
        return response()->download($pathFull);
      } catch (Exception $e) {
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
        return response()->json([
          'type' => 'success',
          'message' => 'Archivo subido con éxito',
          'data' => $file
        ], 200);
      }
    }

    public function generateTemplate(Request $request)
    {
      if ($request->fromDate && $request->toDate) {
        $outgoingFiling = OutgoingFiling::with(
          'upFiles',
          'people',
          'TypeDocument:id,name',
          'ContextType:id,name',
          'dependence:id,names',
          'Priority:id,name'
          )
          ->where('state', 1)
          ->whereBetween('created_at', [$request->fromDate, $request->toDate])
          ->get();
      }else{
        $outgoingFiling = OutgoingFiling::with(
          'upFiles',
          'people',
          'TypeDocument:id,name',
          'ContextType:id,name',
          'dependence:id,names',
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

      $pdf = PDF::loadView('Pdf.templateOutgoingFiling', compact(
        'outgoingFiling', 'company', 'rangeDate' , 'fromDate', 'toDate'
      ))->setPaper('a4', 'landscape');
      //$pdf->set_option('isPhpEnabled', true);
      return $pdf->stream('Planilla.pdf');
    }
}
