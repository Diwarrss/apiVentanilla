<?php

namespace App\Http\Controllers\API;

use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return Company::first();
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeImage(Request $request)
    {
      $company = Company::first();//Busca registro por ID
      //cambia la imagen del usuario
      if ($company) {
        if($request->hasFile('image')) {//valida que venga la imagen en los parametros de la petición
          $fileExt = $request->image->getClientOriginalExtension();//captura la extención del archivo
          $path =  "uploads/companyImage";//crea la ruta de la imagen en el servidor
          File::deleteDirectory("storage/$path");
          if (!file_exists("storage/$path")) {//revisa si existe la ruta y si no la crea
            File::makeDirectory("storage/$path", 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(//pone la nueva imagen el el servidor con su respectivo nombre
            $path , $request->image , 'image.' . $fileExt
          );
          $company->image = "/storage/$pathFull";
          $company->save();//guarda la ruta de la imagen el la base de datos
        }

        if ($company) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $company
          ], 202);
        }else{//respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al actualizar',
            'data' => []
          ], 204);
        }

      }
    }

    public function changeLogo(Request $request)
    {
      $company = Company::first();//Busca registro por ID
      //cambia la imagen del usuario
      if ($company) {
        if($request->hasFile('image')) {//valida que venga la imagen en los parametros de la petición
          $fileExt = $request->image->getClientOriginalExtension();//captura la extención del archivo
          $path =  "uploads/companyLogo";//crea la ruta de la imagen en el servidor
          File::deleteDirectory("storage/$path");
          if (!file_exists("storage/$path")) {//revisa si existe la ruta y si no la crea
            File::makeDirectory("storage/$path", 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(//pone la nueva imagen el el servidor con su respectivo nombre
            $path , $request->image , 'logo.' . $fileExt
          );
          $company->logo = "/storage/$pathFull";
          $company->save();//guarda la ruta de la imagen el la base de datos
        }

        if ($company) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $company
          ], 202);
        }else{//respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al actualizar',
            'data' => []
          ], 204);
        }

      }
    }
}
