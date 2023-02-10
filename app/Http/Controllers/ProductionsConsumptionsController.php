<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionsOrder;
use App\Models\ProductionsConsumption;
use App\Http\Controllers\ProductionsConsumptionsItemsController as ConsumptionsItems;

class ProductionsConsumptionsController extends Controller
{

    public function __construct() {
        $this->middleware('can:productions_consumptions.store')->only('store');
        $this->middleware('can:productions_consumptions.show')->only('show');
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
