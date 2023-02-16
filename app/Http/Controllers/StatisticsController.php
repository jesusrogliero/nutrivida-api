<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatisticsController extends Controller
{

    public function get_statistics(Request $request)
    {
        try{  
           
            $purchases_statistics = \DB::table('purchases_orders')
            ->selectRaw('SUM(purchases_orders.total_load) as quantity')
            ->where('purchases_orders.state_id', '=', 2)
            ->where('created_at', '>', 'INTERVAL ' . $request->interval . ' ' . $request->interval_period)
            ->first();

            $consumptions_statistics = \DB::table('productions_consumptions')
            ->join('productions_orders', 'productions_orders.id', '=', 'productions_consumptions.production_order_id')
            ->selectRaw('SUM(productions_consumptions.consumption_production) as quantity')
            ->where('productions_orders.state_id', '=', 2)
            ->where('productions_consumptions.created_at', '>', 'INTERVAL ' . $request->interval . ' ' . $request->interval_period)
            ->first();

            $finals_to_warehouse_statistics = \DB::table('products_finals_to_warehouses')
            ->selectRaw('SUM(products_finals_to_warehouses.quantity) as quantity')
            ->where('products_finals_to_warehouses.state_id', '=', 2)
            ->where('products_finals_to_warehouses.date', '>', 'INTERVAL ' . $request->interval . ' ' . $request->interval_period)
            ->first();

            return response()->json([
                'purchases_statistics' => $purchases_statistics->quantity,
                'consumptions_statistics' => $consumptions_statistics->quantity,
                'finals_to_warehouse_statistics' => $finals_to_warehouse_statistics->quantity
            ]);

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

    public function get_purchases_data_chart(Request $request)
    {
        try{  
            $purchases_charts =  \DB::select("SELECT DATE_FORMAT( purchases_orders.created_at, '%c' ) AS MONTH,
            SUM(purchases_orders.total_load) AS total FROM purchases_orders 
            WHERE purchases_orders.state_id = 2 AND
            DATE_FORMAT( purchases_orders.created_at, '%Y' ) = DATE_FORMAT(NOW(), '%Y')
            GROUP BY MONTH( purchases_orders.created_at )");

            $purchases_charts = \collect($purchases_charts);
            
            $dataset = [
                'label' => 'Ingreso',
                'backgroundColor' => 'green',
                'data' => null
            ];

            $data = array();

            for($i=1; $i<=12; $i++) {
                $item = $purchases_charts->where('MONTH', $i);
                
                if(  $item->isEmpty() )
                    array_push($data, 0);
                else
                    array_push($data, floatval( $item->sole()->total ) );
                
            }
    
            $dataset['data'] = $data;

            return response()->json($dataset);

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


    public function get_prd_final_data_chart(Request $request)
    {
        try{

            $products_final_data =  \DB::select("SELECT DATE_FORMAT( products_finals_to_warehouses.created_at, '%c' ) AS MONTH,
            SUM(products_finals_to_warehouses.quantity) AS total FROM products_finals_to_warehouses 
            WHERE products_finals_to_warehouses.state_id = 2 AND
            DATE_FORMAT( products_finals_to_warehouses.created_at, '%Y' ) = DATE_FORMAT(NOW(), '%Y')
            GROUP BY MONTH(products_finals_to_warehouses.created_at)");

            $products_final_data = \collect($products_final_data);
            
            $dataset = [
                'label' => 'Ingresos',
                'backgroundColor' => 'blue',
                'data' => null
            ];

            $data = array();

            for($i=1; $i<=12; $i++) {
                $item = $products_final_data->where('MONTH', $i);
                
                if(  $item->isEmpty() )
                    array_push($data, 0);
                else
                    array_push($data, floatval( $item->sole()->total ) );
                
            }
    
            $dataset['data'] = $data;

            return response()->json($dataset);

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


    public function get_consumption_data_chart(Request $request)
    {
        try{

            $consumption_charts =  \DB::select("SELECT DATE_FORMAT( productions_consumptions.created_at, '%c' ) AS MONTH,
            SUM(productions_consumptions.consumption_production) AS total FROM productions_consumptions
            INNER JOIN productions_orders on productions_orders.id = productions_consumptions.production_order_id
            WHERE productions_orders.state_id = 2 AND
            DATE_FORMAT( productions_consumptions.created_at, '%Y' ) = DATE_FORMAT(NOW(), '%Y')
            GROUP BY MONTH(productions_consumptions.created_at)");

            $consumption_charts = \collect($consumption_charts);
            
            $dataset = [
                'label' => 'Ingreso',
                'backgroundColor' => 'red',
                'data' => null
            ];

            $data = array();

            for($i=1; $i<=12; $i++) {
                $item = $consumption_charts->where('MONTH', $i);
                
                if(  $item->isEmpty() )
                    array_push($data, 0);
                else{
                    array_push($data, floatval( $item->sole()->total ) );
                
                }
                
            }
    
            $dataset['data'] = $data;

            return response()->json($dataset);

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
