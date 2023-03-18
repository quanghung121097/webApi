<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $table = 'order';
    public function customer(){
    	return $this->belongsTo(Customer::class);
    }
    public function order_items(){
    	return $this->hasMany(OrderItem::class);
    }
    public function shipping_address(){
    	return $this->belongsTo(ShippingAddress::class);
    }
    public function payment(){
    	return $this->belongsTo(Payment::class);
    }
}
