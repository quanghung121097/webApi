<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class Account extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;
	protected $table = 'account';
	protected $fillable =['id','username','password','role'];
	protected $hidden = ['password','remember_token'];
	public $timestamps = true;
	public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }
	public function person(){
		return $this->hasOne(Person::class);
	}
}
