<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
	use SoftDeletes;
	protected $table ="product";
	public function category(){
		return $this->belongsTo(Category::class,'category_id','id');
	}
	public function images(){
		return $this->hasMany(Image::class,'product_id','id');
	}
	public function order_items(){
		return $this->hasMany(OrderItem::class,'product_id','id');
	}
}
