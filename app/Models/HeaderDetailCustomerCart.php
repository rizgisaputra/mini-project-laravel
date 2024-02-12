<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderDetailCustomerCart extends Model
{
    use HasFactory;
    protected $table = 'headers_detail_customers_carts';
    protected $guarded = [];
}
