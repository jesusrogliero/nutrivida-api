<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuppliesMinor;

class SuppliesMinorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $supplies = [
            [ 'name' => 'Trila.Chicha 1Kg',   'stock' => 25929.55, 'consumption_weight_package' =>  0.015 ],
            [ 'name' => 'Trila.Cereal 1Kg.',  'stock' => 10357.36,  'consumption_weight_package' =>  0.015 ],
            [ 'name' => 'Trila.Chicha 2Kg.',  'stock' => 268,       'consumption_weight_package' =>  0.02  ],
            [ 'name' => 'Trila.Chicha 500g.', 'stock' => 12002,     'consumption_weight_package' =>  0.009 ],
            [ 'name' => 'Trila.Cereal 500g.', 'stock' => 0,         'consumption_weight_package' =>  0.009 ],
            [ 'name' => 'Bilaminado Cereal.', 'stock' => 0,         'consumption_weight_package' =>  0.0082],
            [ 'name' => 'Bilaminado Chicha',  'stock' => 6869,      'consumption_weight_package' =>  0.0082],
            [ 'name' => 'Polietileno Crema A.',  'stock' => 1709.21, 'consumption_weight_package' =>  0.0077],
            [ 'name' => 'Polietileno Fororo',  'stock' => 4400.77,  'consumption_weight_package' =>  0.0077],
            [ 'name' => 'BOLSONES (Unid.)',  'stock' => 2758.25,    'consumption_weight_package' => 0 ],
            [ 'name' => 'ENVOPLAS ( Kg.)',  'stock' => 4206.76,     'consumption_weight_package'  =>   960.0 ],
        ];

        $supplies[8]['consumption_weight_package'] =+ $supplies[0]['consumption_weight_package'] / 12;
        $supplies[8]['consumption_weight_package'] =+ $supplies[1]['consumption_weight_package'] / 12;
        $supplies[8]['consumption_weight_package'] =+ $supplies[2]['consumption_weight_package'] / 6;
        $supplies[8]['consumption_weight_package'] =+ $supplies[3]['consumption_weight_package'] / 24;
        $supplies[8]['consumption_weight_package'] =+ $supplies[4]['consumption_weight_package'] / 24;
        $supplies[8]['consumption_weight_package'] =+ $supplies[5]['consumption_weight_package'] / 12;
        $supplies[8]['consumption_weight_package'] =+ $supplies[6]['consumption_weight_package'] / 12; 
        $supplies[8]['consumption_weight_package'] =+ $supplies[7]['consumption_weight_package'] / 12; 
        $supplies[8]['consumption_weight_package'] =+ $supplies[8]['consumption_weight_package'] / 12;

        $supplies[8]['consumption_weight_package'] = number_format( $supplies[8]['consumption_weight_package'], 2);
       
        foreach ($supplies as $supply) {
            SuppliesMinor::firstOrCreate($supply, $supply);
        }
    }
}
