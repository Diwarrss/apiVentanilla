<?php

namespace App\Http\Controllers\API;

use App\EntryFiling;
use App\Http\Controllers\Controller;
use App\OutgoingFiling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchFilingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {//retorna la información de la base de datos
      if ($request->typeSearch === "1") { //es para el endpoint de mis radicados
        $user = Auth::user()->dependencePerson_id;
        $userDependence = Auth::user()->dependence_id;
        if ($request->type === "0") { //type=0 es para radicacion de entrada
          if ($request->fromDate && $request->toDate) {//retorna l a información que este en dos rangos de fecha especificos
            return EntryFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->whereHas('dependences', function($query) use ($user, $userDependence)  {//condicion en la relación
                //retorna los radicados que tienen como destinatario al usuario a a la dependencia a la que pertenece
                $query->where('entry_filing_has_dependences.dependence_id', $user)
                      ->orWhere('entry_filing_has_dependences.dependence_id', $userDependence);
              })
              ->where('state', 1)
              ->whereBetween('created_at', [$request->fromDate, $request->toDate])
              ->get();
          } else {//retorna l a información que este tenga fecha de hoy
            return EntryFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->whereHas('dependences', function($query) use ($user, $userDependence)  {//condicion en la relación
                //retorna los radicados que tienen como destinatario al usuario a a la dependencia a la que pertenece
                $query->where('entry_filing_has_dependences.dependence_id', $user)
                      ->orWhere('entry_filing_has_dependences.dependence_id', $userDependence);
              })
              ->where('state', 1)
              ->whereDate('created_at', now())
              ->get();
          }
        }else if ($request->type === "1") { //type=1 es para radicacion de entrada
          if ($request->fromDate && $request->toDate) {//retorna l a información que este en dos rangos de fecha especificos
            return OutgoingFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->where('state', 1)
              ->where('dependence_id', $user)
              ->whereBetween('created_at', [$request->fromDate, $request->toDate])
              ->get();
          } else {//retorna l a información que tenga fecha de hoy
            return OutgoingFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->where('state', 1)
              ->where('dependence_id', $user)
              ->whereDate('created_at', now())
              ->get();
          }
        }
      } else { //es el endpoint de buscar radicados
        //se crean arrays para hacer condiciones dinamicas
        $matchThese = [];
        $title = [];
        $typeDocument = [];
        $sender = [];
        $addressee_id = $request->addressee;
        //Revisa el parámetro title para llenar el array condicion
        if ($request->title) {
          $title = ['title', 'like', "%$request->title%"];
          array_push($matchThese, $title);
        }
        //Revisa el parámetro typeDocument para llenar el array condicion
        if ($request->typeDocument) {
          $typeDocument = ['type_document_id', '=', $request->typeDocument];
          array_push($matchThese, $typeDocument);
        }
        //Revisa el parámetro sender para llenar el array de su respectiva condicion
        if ($request->sender) {
          if ($request->type === "0") {
            $sender = ['dependence_id', '=', $request->sender];
            array_push($matchThese, $sender);
          } else if ($request->type === "1") {
            $sender = ['dependence_id', '=', $request->sender];
            array_push($matchThese, $sender);
          }
        }
        if ($request->type === "0") { //type=0 es para radicacion de entrada
          if ($request->setledSearch) {
            //retorna la informacion que pertenece al numero de radicado
            return EntryFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->where('settled', $request->setledSearch)
              ->where('state', 1)
              ->get();
          }
          if ($request->addressee) {
            //retorna la informacion que pertenece al destinatario
            if ($request->fromDate && $request->toDate) {
              return EntryFiling::with(
                'upFiles',
                'dependences',
                'TypeDocument:id,name',
                'ContextType:id,name',
                'dependence:id,names',
                'Priority:id,name'
                )
                ->whereHas('dependences', function($query) use ($addressee_id)  {//condicion de la relacion de las tablas
                  $query->where('entry_filing_has_dependences.dependence_id', $addressee_id);
                })
                ->where($matchThese)
                ->where('state', 1)
                ->whereBetween('created_at', [$request->fromDate, $request->toDate])
                /* ->whereBetween('created_at', [$request->fromDate." 00:00:00", $request->toDate." 23:59:59"]) */
                ->get();
            } else {
              return EntryFiling::with(
                'upFiles',
                'dependences',
                'TypeDocument:id,name',
                'ContextType:id,name',
                'dependence:id,names',
                'Priority:id,name'
                )
                ->whereHas('dependences', function($query) use ($addressee_id)  {//condicion de la relacion de las tablas
                  $query->where('entry_filing_has_dependences.dependence_id', $addressee_id);
                })
                ->where($matchThese)
                ->where('state', 1)
                /* ->whereBetween('created_at', [$request->fromDate, $request->toDate]) */
                /* ->whereBetween('created_at', [$request->fromDate." 00:00:00", $request->toDate." 23:59:59"]) */
                ->get();
            }
          }
          //si tiene algun parametro de busqueda retorna entre fehcas seleccionadas o toda la infomracion sin flitro de fecha
          if ($request->title || $request->typeDocument || $request->sender) {
            if ($request->fromDate && $request->toDate) {
              return EntryFiling::with(
                'upFiles',
                'dependences',
                'TypeDocument:id,name',
                'ContextType:id,name',
                'dependence:id,names',
                'Priority:id,name'
                )
                ->where($matchThese)
                ->where('state', 1)
                ->whereBetween('created_at', [$request->fromDate, $request->toDate])
                ->get();
            } else {
              return EntryFiling::with(
                'upFiles',
                'dependences',
                'TypeDocument:id,name',
                'ContextType:id,name',
                'dependence:id,names',
                'Priority:id,name'
                )
                ->where($matchThese)
                ->where('state', 1)
                /* ->whereDate('created_at', now()) */
                ->get();
            }
          }
          //retorna la informacion que perteneces a toda la data sin numero de radicado ni destinatario pero solo del dia actual
          if ($request->fromDate && $request->toDate) {
            return EntryFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->where($matchThese)
              ->where('state', 1)
              ->whereBetween('created_at', [$request->fromDate, $request->toDate])
              ->get();
          } else {
            return EntryFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->where($matchThese)
              ->where('state', 1)
              ->whereDate('created_at', now())
              ->get();
          }
        }else if ($request->type === "1") { //type=1 es para radicacion de salida
          if ($request->setledSearch) {
            //retorna la informacion que pertenece al numero de radicado
            return OutgoingFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->where('state', 1)
              ->where('settled', $request->setledSearch)
              ->get();
          }
          if ($request->addressee) {
            //retorna la informacion que pertenece al destinatario
            if ($request->fromDate && $request->toDate) {
              return OutgoingFiling::with(
                'upFiles',
                'dependences',
                'TypeDocument:id,name',
                'ContextType:id,name',
                'dependence:id,names',
                'Priority:id,name'
                )
                ->whereHas('dependences', function($query) use ($addressee_id)  {//condicion en una relacion de la base de datos
                  $query->where('outgoing_filing_has_dependences.dependence_id', $addressee_id);
                })
                ->where($matchThese)
                ->where('state', 1)
                ->whereBetween('created_at', [$request->fromDate, $request->toDate])
                ->get();
            } else {
              return OutgoingFiling::with(
                'upFiles',
                'dependences',
                'TypeDocument:id,name',
                'ContextType:id,name',
                'dependence:id,names',
                'Priority:id,name'
                )
                ->whereHas('dependences', function($query) use ($addressee_id)  {//condicion en una relacion de la base de datos
                  $query->where('outgoing_filing_has_dependences.dependence_id', $addressee_id);
                })
                ->where($matchThese)
                ->where('state', 1)
                /* ->whereBetween('created_at', [$request->fromDate, $request->toDate]) */
                ->get();
            }
          }
          //retorna la informacion completa si no tiene parametros de fecha, solo si verifica algun parametro de busqueda
          if ($request->title || $request->typeDocument || $request->sender) {
            if ($request->fromDate && $request->toDate) {
              return OutgoingFiling::with(
                'upFiles',
                'dependences',
                'TypeDocument:id,name',
                'ContextType:id,name',
                'dependence:id,names',
                'Priority:id,name'
                )
                ->where($matchThese)
                ->where('state', 1)
                ->whereBetween('created_at', [$request->fromDate, $request->toDate])
                ->get();
            } else {
              return OutgoingFiling::with(
                'upFiles',
                'dependences',
                'TypeDocument:id,name',
                'ContextType:id,name',
                'dependence:id,names',
                'Priority:id,name'
                )
                ->where($matchThese)
                ->where('state', 1)
                /* ->whereDate('created_at', now()) */
                ->get();
            }
          }
          //retorna la informacion que perteneces a toda la data sin numero de radicado ni destinatario pero solo del dia actual
          if ($request->fromDate && $request->toDate) {
            return OutgoingFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->where($matchThese)
              ->where('state', 1)
              ->whereBetween('created_at', [$request->fromDate, $request->toDate])
              ->get();
          } else {
            return OutgoingFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->where($matchThese)
              ->where('state', 1)
              ->whereDate('created_at', now())
              ->get();
          }
        }
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
        //
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
        //
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

    //download XLSX
    public function export(Request $request)
    {
      //return $request;
      $matchThese = [];
      $title = [];
      $typeDocument = [];
      $sender = [];
      $addressee_id = $request->addressee;
      //Revisa el parámetro title para llenar el array condicion
      if ($request->title) {
        $title = ['title', 'like', "%$request->title%"];
        array_push($matchThese, $title);
      }
      //Revisa el parámetro typeDocument para llenar el array condicion
      if ($request->typeDocument) {
        $typeDocument = ['type_document_id', '=', $request->typeDocument];
        array_push($matchThese, $typeDocument);
      }
      //Revisa el parámetro sender para llenar el array de su respectiva condicion
      if ($request->sender) {
        if ($request->type === "0") {
          $sender = ['entry_filings.dependence_id', '=', $request->sender];
          array_push($matchThese, $sender);
        } else if ($request->type === "1") {
          $sender = ['outgoing_filings.dependence_id', '=', $request->sender];
          array_push($matchThese, $sender);
        }
      }
      if ($request->type === "0") { //type=0 es para radicacion de entrada
        if ($request->setledSearch) {
          return EntryFiling::Join ('dependences as rem', 'entry_filings.dependence_id', '=', 'rem.id')
                          ->join('entry_filing_has_dependences', 'entry_filing_has_dependences.entry_filing_id', '=', 'entry_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'entry_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'entry_filings.type_document_id', '=', 'type_documents.id')
                          ->select('entry_filings.id as ID', 'entry_filings.settled as Radicado', 'entry_filings.created_at as Fecha', DB::raw("(CASE entry_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'entry_filings.title as Titulo', 'entry_filings.subject as Asunto', 'entry_filings.folios as Folios', 'entry_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE entry_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          ->where('entry_filings.state', 1)
                          ->where('settled', $request->setledSearch)
                          ->get();
        }
        if ($request->addressee) {
          if ($request->fromDate != $request->toDate) {
            return EntryFiling::Join ('dependences as rem', 'entry_filings.dependence_id', '=', 'rem.id')
                          ->join('entry_filing_has_dependences', 'entry_filing_has_dependences.entry_filing_id', '=', 'entry_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'entry_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'entry_filings.type_document_id', '=', 'type_documents.id')
                          ->select('entry_filings.id as ID', 'entry_filings.settled as Radicado', 'entry_filings.created_at as Fecha', DB::raw("(CASE entry_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'entry_filings.title as Titulo', 'entry_filings.subject as Asunto', 'entry_filings.folios as Folios', 'entry_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE entry_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          ->whereBetween('entry_filings.created_at', [$request->fromDate, $request->toDate])
                          ->where('entry_filing_has_dependences.dependence_id', $addressee_id)
                          ->where($matchThese)
                          ->where('entry_filings.state', 1)
                          ->get();
          } else {
            return EntryFiling::Join ('dependences as rem', 'entry_filings.dependence_id', '=', 'rem.id')
                          ->join('entry_filing_has_dependences', 'entry_filing_has_dependences.entry_filing_id', '=', 'entry_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'entry_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'entry_filings.type_document_id', '=', 'type_documents.id')
                          ->select('entry_filings.id as ID', 'entry_filings.settled as Radicado', 'entry_filings.created_at as Fecha', DB::raw("(CASE entry_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'entry_filings.title as Titulo', 'entry_filings.subject as Asunto', 'entry_filings.folios as Folios', 'entry_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE entry_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          /* ->whereBetween('entry_filings.created_at', [$request->fromDate, $request->toDate]) */
                          ->where('entry_filing_has_dependences.dependence_id', $addressee_id)
                          ->where($matchThese)
                          ->where('entry_filings.state', 1)
                          ->get();
          }
        }
        if ($request->title || $request->typeDocument || $request->sender) {
          if ($request->fromDate && $request->toDate) {
            return EntryFiling::Join ('dependences as rem', 'entry_filings.dependence_id', '=', 'rem.id')
                          ->join('entry_filing_has_dependences', 'entry_filing_has_dependences.entry_filing_id', '=', 'entry_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'entry_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'entry_filings.type_document_id', '=', 'type_documents.id')
                          ->select('entry_filings.id as ID', 'entry_filings.settled as Radicado', 'entry_filings.created_at as Fecha', DB::raw("(CASE entry_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'entry_filings.title as Titulo', 'entry_filings.subject as Asunto', 'entry_filings.folios as Folios', 'entry_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE entry_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          ->whereBetween('entry_filings.created_at', [$request->fromDate, $request->toDate])
                          ->where('entry_filings.state', 1)
                          ->where($matchThese)
                          ->get();
          } else {
            return EntryFiling::Join ('dependences as rem', 'entry_filings.dependence_id', '=', 'rem.id')
                          ->join('entry_filing_has_dependences', 'entry_filing_has_dependences.entry_filing_id', '=', 'entry_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'entry_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'entry_filings.type_document_id', '=', 'type_documents.id')
                          ->select('entry_filings.id as ID', 'entry_filings.settled as Radicado', 'entry_filings.created_at as Fecha', DB::raw("(CASE entry_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'entry_filings.title as Titulo', 'entry_filings.subject as Asunto', 'entry_filings.folios as Folios', 'entry_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE entry_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          //->whereBetween('entry_filings.created_at', [$request->fromDate, $request->toDate])
                          ->where('entry_filings.state', 1)
                          ->where($matchThese)
                          ->get();
          }
        }
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
                          ->whereDate('entry_filings.created_at', now())
                          ->where('entry_filings.state', 1)
                          ->get();
        }
      } else if ($request->type === "1") { //type=1 es para radicacion de salida
        if ($request->setledSearch) {
          return OutgoingFiling::Join ('dependences as rem', 'outgoing_filings.dependence_id', '=', 'rem.id')
                          ->join('outgoing_filing_has_dependences', 'outgoing_filing_has_dependences.outgoing_filing_id', '=', 'outgoing_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'outgoing_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'outgoing_filings.type_document_id', '=', 'type_documents.id')
                          ->select('outgoing_filings.id as ID', 'outgoing_filings.settled as Radicado', 'outgoing_filings.created_at as Fecha', DB::raw("(CASE outgoing_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'outgoing_filings.title as Titulo', 'outgoing_filings.subject as Asunto', 'outgoing_filings.folios as Folios', 'outgoing_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE outgoing_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          //->whereBetween('outgoing_filings.created_at', [$request->fromDate, $request->toDate])
                          ->where('settled', $request->setledSearch)
                          ->where('outgoing_filings.state', 1)
                          ->get();
        }
        if ($request->addressee) {
          //retorna la informacion que pertenece al destinatario
          if ($request->fromDate && $request->toDate) {
            return OutgoingFiling::Join ('dependences as rem', 'outgoing_filings.dependence_id', '=', 'rem.id')
                          ->join('outgoing_filing_has_dependences', 'outgoing_filing_has_dependences.outgoing_filing_id', '=', 'outgoing_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'outgoing_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'outgoing_filings.type_document_id', '=', 'type_documents.id')
                          ->select('outgoing_filings.id as ID', 'outgoing_filings.settled as Radicado', 'outgoing_filings.created_at as Fecha', DB::raw("(CASE outgoing_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'outgoing_filings.title as Titulo', 'outgoing_filings.subject as Asunto', 'outgoing_filings.folios as Folios', 'outgoing_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE outgoing_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          ->whereBetween('outgoing_filings.created_at', [$request->fromDate, $request->toDate])
                          ->where('outgoing_filing_has_dependences.dependence_id', $addressee_id)
                          ->where($matchThese)
                          ->where('outgoing_filings.state', 1)
                          ->get();
          } else {
            return OutgoingFiling::Join ('dependences as rem', 'outgoing_filings.dependence_id', '=', 'rem.id')
                          ->join('outgoing_filing_has_dependences', 'outgoing_filing_has_dependences.outgoing_filing_id', '=', 'outgoing_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'outgoing_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'outgoing_filings.type_document_id', '=', 'type_documents.id')
                          ->select('outgoing_filings.id as ID', 'outgoing_filings.settled as Radicado', 'outgoing_filings.created_at as Fecha', DB::raw("(CASE outgoing_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'outgoing_filings.title as Titulo', 'outgoing_filings.subject as Asunto', 'outgoing_filings.folios as Folios', 'outgoing_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE outgoing_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          //->whereBetween('outgoing_filings.created_at', [$request->fromDate, $request->toDate])
                          ->where('outgoing_filing_has_dependences.dependence_id', $addressee_id)
                          ->where($matchThese)
                          ->where('outgoing_filings.state', 1)
                          ->get();
          }
        }
        //retorna la informacion completa si no tiene parametros de fecha, solo si verifica algun parametro de busqueda
        if ($request->title || $request->typeDocument || $request->sender) {
          if ($request->fromDate && $request->toDate) {
            return OutgoingFiling::Join ('dependences as rem', 'outgoing_filings.dependence_id', '=', 'rem.id')
                          ->join('outgoing_filing_has_dependences', 'outgoing_filing_has_dependences.outgoing_filing_id', '=', 'outgoing_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'outgoing_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'outgoing_filings.type_document_id', '=', 'type_documents.id')
                          ->select('outgoing_filings.id as ID', 'outgoing_filings.settled as Radicado', 'outgoing_filings.created_at as Fecha', DB::raw("(CASE outgoing_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'outgoing_filings.title as Titulo', 'outgoing_filings.subject as Asunto', 'outgoing_filings.folios as Folios', 'outgoing_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE outgoing_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          ->whereBetween('outgoing_filings.created_at', [$request->fromDate, $request->toDate])
                          ->where($matchThese)
                          ->where('outgoing_filings.state', 1)
                          ->get();
          } else {
            return OutgoingFiling::Join ('dependences as rem', 'outgoing_filings.dependence_id', '=', 'rem.id')
                          ->join('outgoing_filing_has_dependences', 'outgoing_filing_has_dependences.outgoing_filing_id', '=', 'outgoing_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'outgoing_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'outgoing_filings.type_document_id', '=', 'type_documents.id')
                          ->select('outgoing_filings.id as ID', 'outgoing_filings.settled as Radicado', 'outgoing_filings.created_at as Fecha', DB::raw("(CASE outgoing_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'outgoing_filings.title as Titulo', 'outgoing_filings.subject as Asunto', 'outgoing_filings.folios as Folios', 'outgoing_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE outgoing_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          //->whereBetween('outgoing_filings.created_at', [$request->fromDate, $request->toDate])
                          ->where($matchThese)
                          ->where('outgoing_filings.state', 1)
                          ->get();
          }
        }
        if ($request->fromDate && $request->toDate) {
          return OutgoingFiling::Join ('dependences as rem', 'outgoing_filings.dependence_id', '=', 'rem.id')
                          ->join('outgoing_filing_has_dependences', 'outgoing_filing_has_dependences.outgoing_filing_id', '=', 'outgoing_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'outgoing_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'outgoing_filings.type_document_id', '=', 'type_documents.id')
                          ->select('outgoing_filings.id as ID', 'outgoing_filings.settled as Radicado', 'outgoing_filings.created_at as Fecha', DB::raw("(CASE outgoing_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'outgoing_filings.title as Titulo', 'outgoing_filings.subject as Asunto', 'outgoing_filings.folios as Folios', 'outgoing_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE outgoing_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          ->whereBetween('outgoing_filings.created_at', [$request->fromDate, $request->toDate])
                          ->where('outgoing_filings.state', 1)
                          ->get();
        } else {
          return OutgoingFiling::Join ('dependences as rem', 'outgoing_filings.dependence_id', '=', 'rem.id')
                          ->join('outgoing_filing_has_dependences', 'outgoing_filing_has_dependences.outgoing_filing_id', '=', 'outgoing_filings.id')
                          ->join('dependences', 'dependences.id', '=', 'outgoing_filing_has_dependences.dependence_id')
                          ->join('type_documents', 'outgoing_filings.type_document_id', '=', 'type_documents.id')
                          ->select('outgoing_filings.id as ID', 'outgoing_filings.settled as Radicado', 'outgoing_filings.created_at as Fecha', DB::raw("(CASE outgoing_filings.state WHEN 1 THEN 'Activo' ELSE 'Inactivo' END) AS Estado"), 'outgoing_filings.title as Titulo', 'outgoing_filings.subject as Asunto', 'outgoing_filings.folios as Folios', 'outgoing_filings.annexes as Anexos', 'rem.names as Remitente', 'dependences.names as Destinatario', DB::raw("(CASE outgoing_filings.access_level WHEN 'public' THEN 'PÚBLICO' ELSE 'RESTRINGIDO' END) AS Nivel_Acceso"), 'type_documents.name as Tipo_Documento')
                          ->whereDate('outgoing_filings.created_at', now())
                          ->where('outgoing_filings.state', 1)
                          ->get();
        }
      }

    }
}
