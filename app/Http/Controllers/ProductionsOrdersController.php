<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridboxNew;
use App\Models\ProductionsOrder;
use App\Models\ProductionsConsumptionsItem;
use App\Models\ProductionsConsumption;

class ProductionsOrdersController extends Controller
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
                ["field" => "productions_orders.id"],
                ["field" => "product_final", "conditions" => "products_finals.name"],
                ["field" => "formula", "conditions" => "formulas.name"],
                ["field" => "quantity", "conditions" => "CONCAT(FORMAT(productions_orders.quantity, 2), ' Kg')"],
                ["field" => "state", "conditions" => "IF(productions_orders.state_id = 0, 'Pendiente', 'Completado')"],
                ["field" => "productions_orders.created_at"],
                ["field" => "productions_orders.updated_at"]
            ];

           #establezco los joins necesarios
           $params["join"] = [
                [ "type" => "inner", "join" => ["products_finals", "productions_orders.product_final_id", "=", "products_finals.id"] ],
                [ "type" => "inner", "join" => ["formulas", "productions_orders.formula_id", "=", "formulas.id"] ],
            ];
            
            # Obteniendo la lista
            $productions_orders = GridboxNew::pagination("productions_orders", $params, false, $request);
            return response()->json($productions_orders);
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
        try{  
            if(empty($request->product_final_id)) throw new \Exception("Debes seleccionar el producto final", 1);
            if(empty($request->formula_id)) throw new \Exception("Debes selecciona una formula", 1);
            if(empty($request->quantity)) throw new \Exception("La cantidad a producir es requerida", 1);

            $new_order = new ProductionsOrder();
            $new_order->formula_id = $request->formula_id;
            $new_order->product_final_id = $request->product_final_id;
            $new_order->quantity = $request->quantity;
            $new_order->save();

            return response()->json('Formula Creada Correctamente', 201);

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
        try{  
            $order = ProductionsOrder::findOrFail($id);
            return response()->json($order);

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
        try{  
            $order = ProductionsOrder::findOrFail($id);

            if($order->state_id === 1)
                throw new \Exception("No es posible actualizar esta orden de producción", 1);
                
            $order->formula_id = $request->formula_id;
            $order->quantity = $request->quantity;
            $order->product_final_id = $request->product_final_id;

            return response()->json('Orden Actualizada Correctamente', 202);

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
        try{  
            \DB::beginTransaction();

            $production_order = ProductionsOrder::findOrFail($id);
            $consumption_order = ProductionsConsumption::where('production_order_id', $production_order->id)->first();
            $consumption_order_items = ProductionsConsumptionsItem::where('production_consumption_id', $consumption_order->id);

            if($production_order->state_id === 1)
                throw new \Exception("No es posible eliminar esta orden de producción", 1);
                
            $consumption_order_items->delete();
            $consumption_order->delete();
            $production_order->delete();

            \DB::commit();
            return response()->json(null, 204);

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
}
