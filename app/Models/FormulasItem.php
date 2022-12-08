<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulasItem extends Model
{
    use HasFactory;

    protected $fillable = [ 'primary_product_id', 'formula_id', 'quantity' ];
}
