<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'lastname',
        'position',
        'cedula',
        'data_admission',
        'address',
        'city',
        'province',
        'nacionality',
        'phone',
        'genere',
        'date_brith'
    ];
}
