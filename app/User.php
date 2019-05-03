<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

//Zizaco
use Zizaco\Entrust\Traits\EntrustUserTrait;

// JWT
use Tymon\JWTAuth\Contracts\JWTSubject;

//class User extends Authenticatable
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    //Zizaco
    use EntrustUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $primarykey = 'id';

    protected $fillable = [
        'name',
        'apellido',
        'descripcion',
        'email',
        'password',
        'imagen',        
        'activo',        
        'ultimo_login',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    //establecemos las relaciones con el modelo Role, ya que un usuario puede tener varios roles
    //y un rol lo pueden tener varios usuarios
    public function roles(){
        return $this->belongsToMany('App\Role');
    }


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /*  JWT */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }    
}
