<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerShippingAddress extends Model
{

    use SoftDeletes;
    protected $table = 'customer_shipping_address';
    public function customer(){
    	return $this->belongsTo(Customer::class);
    }
}
