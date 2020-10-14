<?php

namespace App\Http\Controllers\API;

use App\Audit;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash as FacadesHash;
use File;
use Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return User::with('roles')->get();//retorna la informacion de los usuarios con us respectivos roles
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestUser $request)
    {//Guarda la informacion del nuevo registro
      try {
        DB::beginTransaction();
        $data = $request->all();//captura los parametros q vienen en la petición
        $data['password'] = FacadesHash::make($request->password);
        $user = User::create($data);//Guarda la informacion del registro
        $user->assignRole($request->rol);//asignar rol al usuario
        DB::commit(); //commit de la transaccion

        //NOTA CUANDO VENGAN VARIOS
        /* foreach($request->firm as $file):
    			$fileName = $file->getClientOriginalName();
          $path =  "uploads/users/$user->id/firm/";
          if (!file_exists($path)) {
            File::makeDirectory($path, 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(
            $path , $request->firm , $fileName, 'public'
          );
        endforeach; */
        //sube la firma del usuario al servidor
        if($request->hasFile('firm')) {
          $fileExt = $request->firm->getClientOriginalExtension();//captura la extención del archivo
          $path =  "uploads/users/$user->id/firm";//captura la ruta en donde va  aponer la firma
          if (!file_exists("storage/$path")) {//valida si no existe la ruta, y si no la crea
            File::makeDirectory("storage/$path", 0777, true, true);//crea la carpeta
          }
          $pathFull = Storage::disk('public')->putFileAs(//sube el archivo en la ruta absoluta con su respectivo nombre
            $path , $request->firm , 'firm.' . $fileExt
          );
          $user->firm = "/storage/$pathFull";
          $user->save();//guarda la ruta d ela firma en la base de datos
        }
        //sube la foto del usuario al servidor
        if($request->hasFile('image')) {
          $fileExt = $request->image->getClientOriginalExtension();//captura la extención del archivo
          $path =  "uploads/users/$user->id/image";//captura la ruta en donde va  aponer la foto
          if (!file_exists("storage/$path")) {//valida si no existe la ruta, y si no la crea
            File::makeDirectory("storage/$path", 0777, true, true);//crea la carpeta
          }
          $pathFull = Storage::disk('public')->putFileAs(//sube el archivo en la ruta absoluta con su respectivo nombre
            $path , $request->image , 'image.' . $fileExt
          );
          $user->image = "/storage/$pathFull";
          $user->save();//guarda la ruta de la foto en la base de datos
        }

        if ($user) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $user
          ], 201);
        }else{//respuesta de error
          return response()->json([
            'type' => 'error',
            'message' => 'Error al guardar',
            'data' => []
          ], 204);
        }
      } catch (Exception $e) {//error en el proceso
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
    {//actualiza la infomracion del registro especifico
      try {
        DB::beginTransaction();
        //validations->valida q el nombre y el e-mail no esten en la base de datos
        $request->validate([
          'username' => 'required|max:30|unique:users,username,' . $id,
          'email' => 'required|max:130|unique:users,email,' . $id
        ]);

        $user = User::find($id);//Busca registro por ID
        //Add data in table audits
        $audit = Audit::create([
          'table' => 'users',
          'action' => 'update',
          'data_id' => $user->id,
          'user_table_id' => $user->id,
          'all_data' => json_encode($user),
          'user_id' => Auth::user()->id
        ]);

        //actualiza la infomracion del registro especifico
        $user->username = $request->username;
        $user->email = $request->email;
        if ($request->password != 'null') {//valida si trae la contrasela para actualizarla
          $user->password = FacadesHash::make($request->password);//actualiza la contraseña si viene en los parametros de la petición
        }
        $user->state = $request->state;
        $user->dependence_id = $request->dependence_id;
        $user->dependencePerson_id = $request->dependencePerson_id;

        //sube la nueva firma al servidor
        if($request->hasFile('firm')) {
          $fileExt = $request->firm->getClientOriginalExtension();
          $path =  "uploads/users/$user->id/firm";
          if (!file_exists("storage/$path")) {
            File::makeDirectory("storage/$path", 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(
            $path , $request->firm , 'firm.' . $fileExt
          );
          $user->firm = "/storage/$pathFull";
        }
        //sube la nueva foto del usuario al servidor
        if($request->hasFile('image')) {
          $fileExt = $request->image->getClientOriginalExtension();
          $path =  "uploads/users/$user->id/image";
          if (!file_exists("storage/$path")) {
            File::makeDirectory("storage/$path", 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(
            $path , $request->image , 'image.' . $fileExt
          );
          $user->image = "/storage/$pathFull";
        }

        $user->save();//Guarda la informacion del registro

        $user->removeRole($request->oldRol);//retira el rol actual del usuario
        $user->assignRole($request->rol);//asigna el nuevo rol al usuario

        DB::commit(); //commit de la transaccion

        if ($user) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $user
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

    public function updateState(Request $request, $id)
    {//cambiar el estado del registro
      try {
        DB::beginTransaction();

        $user = User::find($id);//Busca registro por ID

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'users',
          'action' => $user->state ? 'disable' : 'enable',
          'data_id' => $user->id,
          'user_table_id' => $user->id,
          'all_data' => json_encode($user),
          'user_id' => Auth::user()->id
        ]);

        $user->state = !$user->state;//cambia el estado del registro
        $user->save();//guarda el estado del registro

        DB::commit(); //commit de la transaccion

        if ($user) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $user->state
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

    //Cambiar contraseña de usuario logueado (acount)
    public function changePassword(Request $request)
    {//cambiar la contraseña de usuario
      /*
      * Validate all input fields
      */
      $request->validate([
        'old_password'     => 'required',//valida que la contraseña consida con la que tiene la base de datos
        'new_password'     => 'required|min:8',//valida que la nueva ocntraseña cumpla con los parametros necesarios
        'confirm_password' => 'required|same:new_password',//valida que consida con la nueva conrtaseña
      ]);
      $data = $request->all();//captura los parametros q vienen en la petición
      $user = User::find(Auth::user()->id);//Busca registro por ID
      //return FacadesHash::check($request->old_password, $user->password);
      if ($user) {
        if (FacadesHash::check($request->old_password, $user->password)) {//validamos que la iformacion de la contraseña actual coincida
          // The passwords match...
          //guarda la nueva contraseña encriptada en la base de datos
          $user->fill([
            'password' => FacadesHash::make($request->new_password)
          ])->save();

          return response()->json([//respuesta de exito
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $user->state
          ], 202);
        }else{//no conincide la contraseña actual con la información
          return response()->json([
            'type' => 'error',
            'message' => 'la contraseña actual no coincide',
            'data' => []
          ], 202);
        }
      }
    }

    //cambiar username y e-mail de usuario logueado (acount)
    public function changeData(Request $request)
    {
      /*
      * Validate all input fields
      //validations->valida q el nombre y el e-mail no esten en la base de datos
      */
      $request->validate([
        'username' => 'required|max:30|unique:users,username,' . Auth::user()->id,
        'email' => 'required|max:130|unique:users,email,' . Auth::user()->id
      ]);

      $user = User::find(Auth::user()->id);//Busca registro por ID
      if ($user) {
        $user->username = $request->username;
        $user->email = $request->email;
        $user->save();//Guarda la informacion del registro

        if ($user) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $user
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

    //cambiar imagen de usuario logueado
    public function changeImage(Request $request)
    {
      /*
      * Validate all input fields
      */
      $user = User::find(Auth::user()->id);//Busca registro por ID
      //cambia la imagen del usuario
      if ($user) {
        if($request->hasFile('image')) {//valida que venga la imagen en los parametros de la petición
          $fileExt = $request->image->getClientOriginalExtension();//captura la extención del archivo
          $path =  "uploads/users/$user->id/image";//crea la ruta de la imagen en el servidor
          if (!file_exists("storage/$path")) {//revisa si existe la ruta y si no la crea
            File::makeDirectory("storage/$path", 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(//pone la nueva imagen el el servidor con su respectivo nombre
            $path , $request->image , 'image.' . $fileExt
          );
          $user->image = "storage/$pathFull";
          $user->save();//guarda la ruta de la imagen el la base de datos
        }

        if ($user) {//respuesta exitosa
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $user
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
