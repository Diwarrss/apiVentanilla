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
      return User::with('roles')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestUser $request)
    {
      try {
        DB::beginTransaction();
        $data = $request->all();
        $data['password'] = FacadesHash::make($request->password);
        $user = User::create($data);
        //asignar rol
        $user->assignRole($request->rol);
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
        if($request->hasFile('firm')) {
          $fileExt = $request->firm->getClientOriginalExtension();
          $path =  "uploads/users/$user->id/firm";
          if (!file_exists("storage/$path")) {
            File::makeDirectory("storage/$path", 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(
            $path , $request->firm , 'firm.' . $fileExt
          );
          $user->firm = "storage/$pathFull";
          $user->save();
        }
        if($request->hasFile('image')) {
          $fileExt = $request->image->getClientOriginalExtension();
          $path =  "uploads/users/$user->id/image";
          if (!file_exists("storage/$path")) {
            File::makeDirectory("storage/$path", 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(
            $path , $request->image , 'image.' . $fileExt
          );
          $user->image = "storage/$pathFull";
          $user->save();
        }

        if ($user) {
          return response()->json([
            'type' => 'success',
            'message' => 'Creado con éxito',
            'data' => $user
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
        //validations
        //return $request;
        $request->validate([
          'username' => 'required|max:30|unique:users,username,' . $id,
          'email' => 'required|max:130|unique:users,email,' . $id
        ]);

        $user = User::find($id);
        //Add data in table audits
        $audit = Audit::create([
          'table' => 'users',
          'action' => 'update',
          'data_id' => $user->id,
          'user_table_id' => $user->id,
          'all_data' => json_encode($user),
          'user_id' => Auth::user()->id
        ]);

        $user->username = $request->username;
        $user->email = $request->email;
        if ($request->password != 'null') {
          $user->password = FacadesHash::make($request->password);
        }
        $user->state = $request->state;
        $user->dependence_id = $request->dependence_id;
        $user->dependencePerson_id = $request->dependencePerson_id;

        if($request->hasFile('firm')) {
          $fileExt = $request->firm->getClientOriginalExtension();
          $path =  "uploads/users/$user->id/firm";
          if (!file_exists("storage/$path")) {
            File::makeDirectory("storage/$path", 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(
            $path , $request->firm , 'firm.' . $fileExt
          );
          $user->firm = "storage/$pathFull";
        }
        if($request->hasFile('image')) {
          $fileExt = $request->image->getClientOriginalExtension();
          $path =  "uploads/users/$user->id/image";
          if (!file_exists("storage/$path")) {
            File::makeDirectory("storage/$path", 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(
            $path , $request->image , 'image.' . $fileExt
          );
          $user->image = "storage/$pathFull";
        }

        $user->save();

        $user->removeRole($request->oldRol);
        $user->assignRole($request->rol);

        DB::commit(); //commit de la transaccion

        if ($user) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $user
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

    public function updateState(Request $request, $id)
    {
      try {
        DB::beginTransaction();

        /* $data = $request->all(); */
        $user = User::find($id);

        //Add data in table audits
        $audit = Audit::create([
          'table' => 'users',
          'action' => $user->state ? 'disable' : 'enable',
          'data_id' => $user->id,
          'user_table_id' => $user->id,
          'all_data' => json_encode($user),
          'user_id' => Auth::user()->id
        ]);

        $user->state = !$user->state;
        $user->save();

        DB::commit(); //commit de la transaccion

        if ($user) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $user->state
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

    //Cambiar contraseña de usuario logueado (acount)
    public function changePassword(Request $request)
    {
      /*
      * Validate all input fields
      */
      $request->validate([
        'old_password'     => 'required',
        'new_password'     => 'required|min:8',
        'confirm_password' => 'required|same:new_password',
      ]);
      $data = $request->all();
      $user = User::find(Auth::user()->id);
      if ($user) {
        if(!FacadesHash::check($data['old_password'], $user->password)){
          return response()->json([
            'type' => 'error',
            'message' => 'Error al actualizar',
            'data' => []
          ], 204);
        }else{
          $user->fill([
            'password' => FacadesHash::make($request->new_password)
          ])->save();

          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $user->state
          ], 202);
        }
      }
    }

    //cambiar username y e-mail de usuario logueado (acount)
    public function changeData(Request $request)
    {
      /*
      * Validate all input fields
      */
      $request->validate([
        'username' => 'required|max:30|unique:users,username,' . Auth::user()->id,
        'email' => 'required|max:130|unique:users,email,' . Auth::user()->id
      ]);

      $user = User::find(Auth::user()->id);
      if ($user) {
        $user->username = $request->username;
        $user->email = $request->email;
        $user->save();

        if ($user) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $user
          ], 202);
        }else{
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
      $user = User::find(Auth::user()->id);
      if ($user) {
        if($request->hasFile('image')) {
          $fileExt = $request->image->getClientOriginalExtension();
          $path =  "uploads/users/$user->id/image";
          if (!file_exists("storage/$path")) {
            File::makeDirectory("storage/$path", 0777, true, true);
          }
          $pathFull = Storage::disk('public')->putFileAs(
            $path , $request->image , 'image.' . $fileExt
          );
          $user->image = "storage/$pathFull";

          $user->save();
        }

        if ($user) {
          return response()->json([
            'type' => 'success',
            'message' => 'Actualizado con éxito',
            'data' => $user
          ], 202);
        }else{
          return response()->json([
            'type' => 'error',
            'message' => 'Error al actualizar',
            'data' => []
          ], 204);
        }

      }
    }
}
