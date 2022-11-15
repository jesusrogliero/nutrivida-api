<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridboxNew;
use App\Models\NonconformingProduct;
use App\Models\PrimariesProduct;
use App\Models\UsersRole;

class NonconformingProductsController extends Controller
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
                ["field" => "nonconforming_products.id"],
                ["field" => "primary_product", "conditions" => "primaries_products.name"],
                ["field" => "quantity", "conditions" => "CONCAT(FORMAT(nonconforming_products.quantity, 2), ' KG')"],
                ["field" => "nonconforming_products.created_at"],
                ["field" => "nonconforming_products.updated_at"]
            ];

            
            #establezco los joins necesarios
            $params["join"] = [
                [ "type" => "inner", "join" => ["primaries_products", "primaries_products.id", "=", "nonconforming_products.primary_product_id"] ]
            ];
            
            # Obteniendo la lista
            $nonconforming_products = GridboxNew::pagination("nonconforming_products", $params, false, $request);
            return response()->json($nonconforming_products);
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


            \DB::beginTransaction();

            if($request->quantity < 0)
                throw new \Exception("La contidad ingresada es incorrecta");

            if( empty($request->primary_product_id) )
                throw new \Exception("Debe ingresar un producto primario");
                

            $primary_product = PrimariesProduct::findOrFail($request->primary_product_id);

            if($primary_product->stock < $request->quantity)
                throw new \Exception("No hay existencia suficiente de este producto en el inventario", 1);

            $pnc = NonconformingProduct::where('primary_product_id', '=', $request->primary_product_id)->first();

            # si no existe creo un registro nuevoa
            if(empty($pnc))
                $new_PNC = new NonconformingProduct();

            $primary_product->stock = $primary_product->stock - $request->quantity;
            $primary_product->save();

            # Registro el producto
            $pnc->primary_product_id = $request->primary_product_id;
            $pnc->quantity = $pnc->quantity +  $request->quantity;
            $pnc->save();

           \DB::commit();
           return response()->json('Registrado Correctamente', 201);

        } catch(\Exception $e) {
            \DB::rollback();
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
    public function show($id)
    {
        try {
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();
            
            if( $user_role->role_id != 1 && $user_role->role_id != 2) 
                throw new \Exception("Usted No Esta Autorizado Para Realizar Esta Accion", 1);

            $product_NC = NonconformingProduct::findOrFail($id);
            return response()->json($product_NC);

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

            $product_NC = NonconformingProduct::findOrFail($id);
            
            $product_NC->primary_product_id = $request->primary_product_id;
            $product_NC->quantity = $request->quantity;
            $product_NC->observation = $request->observation;

            $product_NC->save();

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
    public function destroy($id)
    {
        try {
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();
            
            if( $user_role->role_id != 1 && $user_role->role_id != 2) 
                throw new \Exception("Usted No Esta Autorizado Para Realizar Esta Accion", 1);

            $product_NC = NonconformingProduct::findOrFail($id);
            $product_NC->delete();

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
