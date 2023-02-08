<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formula;
use App\Models\FormulasItem;

class FormulasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            \DB::beginTransaction();

            $formula = new Formula();
            $formula->name = "Chicha";
            $formula->line_id = 1;
            $formula->quantity_batch = 900;
            $formula->total_formula = 900;
            $formula->save();

            // HArina de Arroz
            $item = new FormulasItem();
            $item->primary_product_id = 1;
            $item->formula_id = $formula->id;
            $item->quantity = 298.8;
            $item->save();

            // Leche Completa
            $item = new FormulasItem();
            $item->primary_product_id = 2;
            $item->formula_id = $formula->id;
            $item->quantity = 281.7;
            $item->save();

            // Azucar
            $item = new FormulasItem();
            $item->primary_product_id = 3;
            $item->formula_id = $formula->id;
            $item->quantity = 315;
            $item->save();

            // Vainilla
            $item = new FormulasItem();
            $item->primary_product_id = 5;
            $item->formula_id = $formula->id;
            $item->quantity = 3.6;
            $item->save();

            // Premix
            $item = new FormulasItem();
            $item->primary_product_id = 6;
            $item->formula_id = $formula->id;
            $item->quantity = 0.9;
            $item->save();

            \DB::commit();

        } catch(\Exception $e) {
            \DB::rollBack();
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
