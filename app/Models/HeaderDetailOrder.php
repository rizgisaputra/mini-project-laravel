<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderDetailOrder extends Model
{
    use HasFactory;
    protected $table = 'header_details_orders';
    protected $guarded = [];
}
