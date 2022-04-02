<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasesOrders extends Model
{
    use HasFactory;

    protected $fillable = [
        'number_order', 'state_id', 'provider_id', 'total_products', 'total_load'
    ];
}
