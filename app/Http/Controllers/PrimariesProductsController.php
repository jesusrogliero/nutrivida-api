<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrimariesProduct;
use App\Models\GridboxNew;

class PrimariesProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $params = $request->all();

            #establezco los campos a mostrar
            $params["select"] = [
                ["field" => "primaries_products.id"],
                ["field" => "name", "conditions" => "primaries_products.name"],
                ["field" => "stock", "conditions" => "CONCAT(FORMAT(primaries_products.stock, 2), ' KG')"],
                ["field" => "primaries_products.created_at"],
                ["field" => "primaries_products.updated_at"]
            ];
            
            # Obteniendo la lista
            $primaries_products = GridboxNew::pagination("primaries_products", $params, false, $request);
            return response()->json($primaries_products);
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

            if( empty($request->name) )
            throw new \Exception("El nombre del producto es obligatorio");
            
            if( $request->stock < 0 )
                throw new \Exception("La existencia no puede ser menor a cero");
                

           $new_product = new PrimariesProduct();
           $new_product->name = $request->name;
           $new_product->stock = $request->stock;
           $new_product->save();

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
    public function get_all_primaries_products()
    {
        try {

            $products = \DB::table('primaries_products')->get();
            return response()->json($products);

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
    public function show($id)
    {
        try {

            $product = PrimariesProduct::findOrFail($id);
            return response()->json($product);

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

            if( empty($request->name) ) throw new \Exception("El nombre del producto es obligatorio");
            if ( empty( $request->stock) ) throw new \Exception("La existencia del producto es obligatoria");
            if( $request->stock < 0 ) throw new \Exception("La existencia no puede ser menor a cero");

            $product = PrimariesProduct::findOrFail($id);
            $product->name = $request->name;
            $product->stock = $request->stock;

            $product->save();

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
            $product = PrimariesProduct::findOrFail($id);
            $product->delete();
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
