<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gridbox;
use App\Models\UsersRole;
use App\Models\PurchasesOrder;
use App\Models\PurchasesOrdersState;

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
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();

            if( $user_role->role_id != 1 && $user_role->role_id != 2 ) {
                throw new \Exception("Usted No Esta Autorizado Para Esta Sección", 1);
            }

            $params = $request->all();

            #establezco los campos a mostrar
            $params["select"] = [
                ["field" => "purchases_orders.id"],
                ["field" => "invoice_number", "conditions" => "purchases_orders.number_invoice"],
                ["field" => "state", "conditions" => "purchases_orders_states.name"],
                ["field" => "provider", "conditions" => "providers.name"],
                ["field" => "total_products", "conditions" => "purchases_orders.total_products"],
                ["field" => "total_load", "conditions" => "purchases_orders.total_load"],
                ["field" => "purchases_orders.created_at"],
                ["field" => "purchases_orders.updated_at"]
            ];

           #establezco los joins necesarios
           $params["join"] = [
                [ "type" => "inner", "join" => ["purchases_orders_states", "purchases_orders_states.id", "=", "purchases_orders.state_id"] ],
                [ "type" => "inner", "join" => ["providers", "providers.id", "=", "purchases_orders.provider_id"] ],
            ];
            
            # Obteniendo la lista
            $purchases_orders = Gridbox::pagination("purchases_orders", $params, false, $request);
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
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();

            if( $user_role->role_id != 1 && $user_role->role_id != 2 ) 
                throw new \Exception("Usted No Esta Autorizado Para Esta Sección", 1);
            
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{  
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();

            if( $user_role->role_id != 1 && $user_role->role_id != 2 ) 
                throw new \Exception("Usted No Esta Autorizado Para Esta Sección", 1);
            
            $new_order = new PurchasesOrder();
            $new_order->number_invoice = $request->number_invoice;
            $new_order->state_id = 1;
            $new_order->provider_id = $request->provider_id;
            $new_order->total_products = 0;
            $new_order->nro_sada_guide = $request->nro_sada_guide;
            $new_order->total_load = 0;
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{  
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();

            if( $user_role->role_id != 1 && $user_role->role_id != 2 ) 
                throw new \Exception("Usted No Esta Autorizado Para Esta Sección", 1);
            
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
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();

            if( $user_role->role_id != 1 && $user_role->role_id != 2 ) 
                throw new \Exception("Usted No Esta Autorizado Para Esta Sección", 1);
            
            if( $order->state_id != 1)
                throw new \Exception("No Es Posible Editar Una Orden Procesada", 1);
            $order = PurchasesOrder::findOrFail($id);
            $order->number_invoice = $request->number_invoice;
            $order->provider_id = $request->provider_id;
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{  
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();

            if( $user_role->role_id != 1 && $user_role->role_id != 2 ) 
                throw new \Exception("Usted No Esta Autorizado Para Esta Sección", 1);

            $order = PurchasesOrder::findOrFail($id);
            
            if($order->state_id != 1)
                throw new \Exception("No es posible eliminar una orden procesada", 1);
            
            $order->delete();
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
