<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionsOrder;
use App\Models\ProductionsConsumption;
use App\Models\ProductionsConsumptionsItem;
use App\Http\Controllers\ProductionsConsumptionsItemsController as ConsumptionsItems;
use App\Models\ConsumptionsSuppliesMinor;
use App\Models\PrimariesProduct;
use App\Models\SuppliesMinor;

class ProductionsConsumptionsController extends Controller
{

    public function __construct() {
        $this->middleware('can:productions_consumptions.store')->only('store');
        $this->middleware('can:productions_consumptions.show')->only('show');
        $this->middleware('can:productions_consumptions.approve_order')->only('approve');
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
            \DB::beginTransaction();

            if(empty($request->production_order_id)) throw new \Exception("La orden de Produccion no ha sido recibida", 1);
            if(empty($request->nro_batch)) throw new \Exception("Antes de Generar por favor ingrese el numro de batch realizados", 1);

            $production_order = ProductionsOrder::findOrFail($request->production_order_id);
            $consumption = null;


            if(empty($request->consumption_id)) {
                
                $consumption = ProductionsConsumption::firstWhere('production_order_id', $request->production_order_id);

                $consumption = new ProductionsConsumption();
                $consumption->production_order_id = $request->production_order_id;
                $consumption->total_production = 0;
                $consumption->consumption_production = 0;
                $consumption->nro_batch = $request->nro_batch;
                $consumption->save();

                ConsumptionsItems::generate_items($consumption, $production_order->formula_id);
            
            } else {
                $consumption = ProductionsConsumption::findOrFail($request->production_order_id);
                $consumption->nro_batch = $request->nro_batch;
                $consumption->total_production = 0;

                ConsumptionsItems::ajust_items($consumption, $production_order->formula_id);
            }



            \DB::commit();
            return response()->json(['message' => 'Orden generada correctamente', 'consumption_id' => $consumption->id], 201);
           
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
     * Apply changes to inventory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request)
    {
        try {
            \DB::beginTransaction();

            $production_order = ProductionsOrder::findOrFail($request->production_order_id);
            $consumption = ProductionsConsumption::where('production_order_id', $production_order->id)->first();
            $consumption_items = ProductionsConsumptionsItem::where('production_consumption_id', $consumption->id)->get();
            $consumption_supply_minor = ConsumptionsSuppliesMinor::where('consumption_id', $consumption->id)->first();
        
            if( !empty($consumption_supply_minor)) {

                $supply_minor = SuppliesMinor::findOrFail($consumption_supply_minor->supply_minor_id);
                $big_bags = SuppliesMinor::findOrFail(10);
                $envoplast = SuppliesMinor::findOrFail(11);

                $supply_minor->stock = $supply_minor->stock - $consumption_supply_minor->consumption;

                if($supply_minor->stock < $consumption_supply_minor->consumption) {
                    throw new \Exception('No hay suficiente '. $supply_minor->name . ' Dentro del inventario');
                }
                $supply_minor->save();
    
                $big_bags->stock = $big_bags->stock - $consumption_supply_minor->consumption_bags;
    
                if($big_bags->stock < $consumption_supply_minor->consumption_bags) {
                    throw new \Exception('No hay suficiente '. $big_bags->name . ' Dentro del inventario');
                }
                 $big_bags->save();
    
                $envoplast->stock = $envoplast->stock - $consumption_supply_minor->envoplast_consumption;
    
                if($envoplast->stock < $consumption_supply_minor->envoplast_consumption) {
                    throw new \Exception('No hay suficiente '. $envoplast->name . ' Dentro del inventario');
                }
                $envoplast->save();

            }
           
            foreach($consumption_items as $item) {
                
                $primary_product = PrimariesProduct::findOrFail($item->primary_product_id);
                
                if($primary_product->stock < $item->to_mixer)
                    throw new \Exception('No hay suficiente '. $primary_product->name . ' Dentro del inventario');

                $primary_product->stock = floatval($primary_product->stock) - floatval($item->to_mixer);

                $primary_product->save();
                $primary_product = null;
            }
           
            $production_order->state_id = true;
            $production_order->save();

            \DB::commit();
            return response()->json('Los cambios han sido reflejados en el inventario correctamente', 202);
           
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
            $consumption = \DB::table('productions_consumptions')
                ->where('productions_consumptions.production_order_id', $id)
                ->first();
            return response()->json($consumption);
           
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
