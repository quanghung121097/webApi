<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingAddress extends Model
{
    use SoftDeletes;
    protected $table = 'shipping_address';
    public function order(){
    	return $this->hasOne(Order::class);
    }
}
