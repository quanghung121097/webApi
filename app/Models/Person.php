<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;
    protected $table = 'person';
    public function account(){
    	return $this->belongsTo(Account::class);
    }
    public function admin(){
    	return $this->hasOne(Admin::class);
    }
    public function customer(){
    	return $this->hasOne(Customer::class);
    }
}
