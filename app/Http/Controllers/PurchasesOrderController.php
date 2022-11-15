<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridboxNew;
use App\Models\UsersRole;
use App\Models\PurchasesOrder;
use App\Models\PurchasesOrdersItem;
use App\Models\PurchasesOrdersState;
use App\Models\PrimariesProduct;
use App\Models\NonconformingProduct;

class PurchasesOrderController extends Controller
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
                ["field" => "purchases_orders.id"],
                ["field" => "nro_sada_guide", "conditions" => "purchases_orders.nro_sada_guide"],
                ["field" => "state", "conditions" => "purchases_orders_states.name"],
                ["field" => "provider", "conditions" => "providers.name"],
                ["field" => "total_products", "conditions" => "purchases_orders.total_products"],
                ["field" => "total_nonconforming", "conditions" => "CONCAT(purchases_orders.total_nonconforming, ' KG')"],
                ["field" => "purchases_orders.created_at"],
                ["field" => "purchases_orders.updated_at"]
            ];

           #establezco los joins necesarios
           $params["join"] = [
                [ "type" => "inner", "join" => ["purchases_orders_states", "purchases_orders_states.id", "=", "purchases_orders.state_id"] ],
                [ "type" => "inner", "join" => ["providers", "providers.id", "=", "purchases_orders.provider_id"] ],
            ];
            
            # Obteniendo la lista
            $purchases_orders = GridboxNew::pagination("purchases_orders", $params, false, $request);
            return response()->json($purchases_orders);
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_purchases_states(Request $request)
    {
        try{
            
            $states = PurchasesOrdersState::findAll();
            return response()->json($states);

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
     * set observation
     *
     * @return \Illuminate\Http\Response
     */
    public function set_observation(Request $request, $id)
    {
        try{
            $order = PurchasesOrder::findOrFail($id);
            $order->observations = $request->observations;
            $order->save();

            return response()->json('Observacion Guardada', 202);

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
            
            $new_order = new PurchasesOrder();
            $new_order->provider_id = $request->provider_id;
            $new_order->nro_sada_guide = $request->nro_sada_guide;
            
            $new_order->save();

            return response()->json('Orden Creada Correctamente', 201);

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
     * Approve an entry order
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, $id)
    {
        try{  
            \DB::beginTransaction();

            $order = PurchasesOrder::findOrFail($id);
            
            if( $order->state_id != 1)
                throw new \Exception("Esta order ya fue Aprobada", 1);
        
            
            $items = PurchasesOrdersItem::where('purchase_order_id','=', $order->id)->get();

            # ingreso los productos al inventario
            foreach ($items as $item){
                $primary_product = PrimariesProduct::findOrFail($item->primary_product_id);

                $primary_product->stock = $primary_product->stock + ($item->quantity - $item->nonconform_quantity);
                $primary_product->save();

                # establezco la cantidad no conforme del producto
                $pnc = NonconformingProduct::where('primary_product_id', '=', $item->primary_product_id)->first();

                # en caso que no exista el registro
                if(empty($pnc)) {
                    $pnc = new NonconformingProduct();
                    $pnc->primary_product_id = $item->primary_product_id;
                }

                $pnc->quantity = $pnc->quantity + $item->nonconform_quantity;
                $pnc->save();
            }

            $order->state_id = 2;
            $order->save();
            
            \DB::commit();
            return response()->json("Orden Ingresada al inventario correctamente", 202);

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
        try{   
            $order = PurchasesOrder::findOrFail($id);
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
            $order = PurchasesOrder::findOrFail($id);

            if( $order->state_id != 1)
            throw new \Exception("No Es Posible Editar Una Orden Procesada", 1);

            $order->provider_id = $request->provider_id;
            $order->nro_sada_guide = $request->nro_sada_guide;
            $order->save();
            
            return response()->json('Orden Actualizada', 202);

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
            $order = PurchasesOrder::findOrFail($id);
            
            if($order->state_id != 1)
                throw new \Exception("No es posible eliminar una orden procesada", 1);
            

            PurchasesOrdersItem::where('purchase_order_id','=', $order->id)->delete();
            $order->delete();

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
