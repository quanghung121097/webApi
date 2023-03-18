<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
	use SoftDeletes;
	protected $table = 'customer';
	public function person(){
		return $this->belongsTo(Person::class);
	}
	public function customer_shipping_addresses(){
		return $this->hasMany(CustomerShippingAddress::class);
	}

	public function orders(){
		return $this->hasMany(Order::class);
	}    
}
