<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;
    protected $table = 'order_item';
    public function review(){
    	return $this->belongsTo(Review::class);
    }
    public function order(){
    	return $this->belongsTo(Order::class);
    }
    public function product(){
    	return $this->belongsTo(Product::class);
    }
}
