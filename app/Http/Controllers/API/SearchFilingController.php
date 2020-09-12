<?php

namespace App\Http\Controllers\API;

use App\EntryFiling;
use App\Http\Controllers\Controller;
use App\OutgoingFiling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
              'People:id,names',
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
              'People:id,names',
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
              'people',
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
              'people',
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
            $sender = ['people_id', '=', $request->sender];
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
              'People:id,names',
              'Priority:id,name'
              )
              ->where('settled', $request->setledSearch)
              ->where('state', 1)
              ->get();
          }
          if ($request->addressee) {
            //retorna la informacion que pertenece al destinatario
            return EntryFiling::with(
              'upFiles',
              'dependences',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'People:id,names',
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
          }
          //retorna la informacion que perteneces a toda la data sin numero de radicado ni destinatario
          return EntryFiling::with(
            'upFiles',
            'dependences',
            'TypeDocument:id,name',
            'ContextType:id,name',
            'People:id,names',
            'Priority:id,name'
            )
            ->where($matchThese)
            ->where('state', 1)
            ->whereBetween('created_at', [$request->fromDate, $request->toDate])
            ->get();
        }else if ($request->type === "1") { //type=1 es para radicacion de salida
          if ($request->setledSearch) {
            //retorna la informacion que pertenece al numero de radicado
            return OutgoingFiling::with(
              'upFiles',
              'people',
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
            return OutgoingFiling::with(
              'upFiles',
              'people',
              'TypeDocument:id,name',
              'ContextType:id,name',
              'dependence:id,names',
              'Priority:id,name'
              )
              ->whereHas('people', function($query) use ($addressee_id)  {//condicion en una relacion de la base de datos
                $query->where('outgoing_filing_has_people.people_id', $addressee_id);
              })
              ->where($matchThese)
              ->where('state', 1)
              ->whereBetween('created_at', [$request->fromDate, $request->toDate])
              ->get();
          }
          //retorna la informacion que perteneces a toda la data sin numero de radicado ni destinatario
          return OutgoingFiling::with(
            'upFiles',
            'people',
            'TypeDocument:id,name',
            'ContextType:id,name',
            'dependence:id,names',
            'Priority:id,name'
            )
            ->where($matchThese)
            ->where('state', 1)
            ->whereBetween('created_at', [$request->fromDate, $request->toDate])
            ->get();
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
}
