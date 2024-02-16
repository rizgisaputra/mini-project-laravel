<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCart extends Model
{
    use HasFactory;
    protected $table = 'customers_carts';
    protected $guarded = [];
}
