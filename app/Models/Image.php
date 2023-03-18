<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;
    protected $table="image";
    public function product(){
    	$this->belongsTo(Product::class,'product_id','id');
    }

    public function uri(){
        return '/images/product/'.$this->name;
    }
}
