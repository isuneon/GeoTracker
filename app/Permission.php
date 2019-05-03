<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustPermission;

//class Permission extends Model
class Permission extends EntrustPermission
{
    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];
 
   //establecemos las relacion de muchos a muchos con el modelo Role, ya que un permiso
   //lo pueden tener varios roles y un rol puede tener varios permisos
   public function roles(){
        return $this->belongsToMany('App\Role');
    }
}
