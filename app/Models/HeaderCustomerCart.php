<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderCustomerCart extends Model
{
    use HasFactory;
    protected $table = 'headers_customers_carts';
    protected $guarded = [];
}
