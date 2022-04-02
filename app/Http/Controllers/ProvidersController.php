<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gridbox;
use App\Models\UsersRole;
use App\Models\Provider;

class ProvidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();

            if( $user_role->role_id != 1 && $user_role->role_id != 2 ) {
                throw new \Exception("Usted No Esta Autorizado Para Esta Sección", 1);
            }

            $params = $request->all();

            #establezco los campos a mostrar
            $params["select"] = [
                ["field" => "providers.id"],
                ["field" => "name", "conditions" => "providers.name"],
                ["field" => "rif", "conditions" => "providers.rif"],
                ["field" => "address", "conditions" => "providers.address"],
                ["field" => "providers.created_at"],
                ["field" => "providers.updated_at"]
            ];
            
            # Obteniendo la lista
            $providers = Gridbox::pagination("providers", $params, false, $request);
            return response()->json($providers);
        } catch(\Exception $e) {
            \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
            return \Response::json([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 422);
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
        try {
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();
            
            if( $user_role->role_id != 1 && $user_role->role_id != 2) 
                throw new \Exception("Usted No Esta Autorizado Para Realizar Esta Accion", 1);


            if( empty($request->name) )
                throw new \Exception("El nombne del provedor es obligatorio");

            if( empty($request->rif) )
                throw new \Exception("Debes ingresar la identificacion del Provedor");
                
            if( empty($request->address) )
                throw new \Exception("Debe ingresar una observacion");
            

            # Registro el provedor
            $new_provider = new Provider();
            $new_provider->name = $request->name;
            $new_provider->rif = $request->rif;
            $new_provider->address =$request->address;
            $new_provider->save();

           return response()->json('Registrado Correctamente', 201);

        } catch(\Exception $e) {
            \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
            return \Response::json([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();
            
            if( $user_role->role_id != 1 && $user_role->role_id != 2) 
                throw new \Exception("Usted No Esta Autorizado Para Realizar Esta Accion", 1);

            $provider = Provider::findOrFail($id);
           return response()->json($provider);

        } catch(\Exception $e) {
            \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
            return \Response::json([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 422);
        }
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
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();
            
            if( $user_role->role_id != 1 && $user_role->role_id != 2) 
                throw new \Exception("Usted No Esta Autorizado Para Realizar Esta Accion", 1);

            $provider = Provider::findOrFail($id);
            
            $provider->name = $request->name;
            $provider->rif = $request->rif;
            $provider->address = $request->address;

            $provider->save();
            return response()->json('Actualizado Correctamente', 202);

        } catch(\Exception $e) {
             \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
             return \Response::json([
                 'file' => $e->getFile(),
                 'line' => $e->getLine(),
                 'message' => $e->getMessage(),
                 'code' => $e->getCode()
             ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();
            
            if( $user_role->role_id != 1 && $user_role->role_id != 2) 
                throw new \Exception("Usted No Esta Autorizado Para Realizar Esta Accion", 1);

            $provider = Provider::findOrFail($id);
            $provider->delete();

            return response()->json(null, 204);

        } catch(\Exception $e) {
             \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
             return \Response::json([
                 'file' => $e->getFile(),
                 'line' => $e->getLine(),
                 'message' => $e->getMessage(),
                 'code' => $e->getCode()
             ], 422);
        }
    }
}
