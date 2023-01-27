<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionsConsumptionsItem;
use App\Models\ProductionsConsumption;
use App\Models\FormulasItem;
use App\Models\GridboxNew;

class ProductionsConsumptionsItemsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_consumption_items(Request $request, $consumption_id)
    {
        try {
            $params = $request->all();

            #establezco los campos a mostrar
            $params["select"] = [
                ["field" => "productions_consumptions_items.id"],
                ["field" => "primary_product", "conditions" => "primaries_products.name"],
                ["field" => "to_mixer", "conditions" => "CONCAT(FORMAT(productions_consumptions_items.to_mixer, 2), ' Kg')"],
                ["field" => "remainder1", "conditions" => "CONCAT(FORMAT(productions_consumptions_items.remainder1, 2), ' Kg')"],
                ["field" => "remainder2", "conditions" => "CONCAT(FORMAT(productions_consumptions_items.remainder2, 2), ' Kg')"],
                ["field" => "consumption_production", "conditions" => "CONCAT(FORMAT(productions_consumptions_items.consumption_production, 2), ' Kg')"],
                ["field" => "consumption_percentage", "conditions" => "CONCAT(productions_consumptions_items.consumption_percentage, ' %')"],
                ["field" => "theoretical_consumption", "conditions" => "CONCAT(productions_consumptions_items.theoretical_consumption, ' Kg')"],
                ["field" => "productions_consumptions_items.created_at"],
                ["field" => "productions_consumptions_items.updated_at"]
            ];

           #establezco los joins necesarios
           $params["join"] = [
                [ "type" => "inner", "join" => ["primaries_products", "primaries_products.id", "=", "productions_consumptions_items.primary_product_id"] ],
            ];

            $params['where'] = [['productions_consumptions_items.production_consumption_id', '=', $consumption_id]];
            
            # Obteniendo la lista
            $productions_consumptions_items = GridboxNew::pagination("productions_consumptions_items", $params, false, $request);
            return response()->json($productions_consumptions_items);
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
     * I generate the default items for production consumption
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function generate_items($production_consumption_id, $formula_id, $nro_batch)
    {
        
            $formulas_items = FormulasItem::find($formula_id)->get();

            foreach ($formulas_items as $formula_item) {
                $consumption_production_item = new ProductionsConsumptionsItem();
                $consumption_production_item->production_consumption_id = $production_consumption_id;
                $consumption_production_item->primary_product_id = $formula_item->primary_product_id;
                $consumption_production_item->to_mixer = 0;
                $consumption_production_item->remainder1 = 0;
                $consumption_production_item->remainder2 = 0;
                $consumption_production_item->consumption_production = 0;
                $consumption_production_item->consumption_percentage = 0;
                $consumption_production_item->theoretical_consumption = $nro_batch * $formula_item->quantity;
                $consumption_production_item->save();
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
            $consumption = ProductionsConsumptionsItem::findOrFail($id);
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
            \DB::beginTransaction();
         
            if(empty($request->to_mixer)) throw new \Exception("Debe ingresar la cantidad enviada al mezclador", 1);
            
            $consumption_item = ProductionsConsumptionsItem::findOrFail($id);
            $consumption = ProductionsConsumption::findOrFail($consumption_item->production_consumption_id);

            $formula_item = \DB::table('formulas_items')
            ->join('productions_orders', 'productions_orders.formula_id', '=', 'formulas_items.formula_id')
            ->select('formulas_items.*')
            ->where('productions_orders.id', '=', $consumption->production_order_id)
            ->first();

            $consumption_item->to_mixer = $request->to_mixer;
            $consumption_item->remainder1 = $request->remainder1;
            $consumption_item->remainder2 = $request->remainder2;

            $consumption_item->consumption_production = $request->to_mixer - ($request->remainder1 + $request->remainder2);
            $consumption_item->theoretical_consumption = $consumption->nro_batch * $formula_item->quantity;

            $consumption->consumption_production = $consumption->consumption_production + $consumption_item->consumption_production;
            $consumption->total_production = $consumption->total_production +   $consumption_item->theoretical_consumption;
            
            $consumption_item->save();
            $consumption->save();

            \DB::commit();
            return response()->json('Guardado Correctamente', 202);

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
