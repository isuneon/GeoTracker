<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustRole;


//class Role extends Model
class Role extends EntrustRole
{

    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];
    
    //  //establecemos las relaciones con el modelo Role, ya que un usuario puede tener varios roles
    // //y un rol lo pueden tener varios usuarios
    // public function permission_role(){
    //     return $this->belongsToMany('App\Permission');
    // }

    //establecemos las relaciones con el modelo Role, ya que un usuario puede tener varios roles
    //y un rol lo pueden tener varios usuarios
    public function permissions(){
        return $this->belongsToMany('App\Permission');
    }
   
    
   //establecemos las relacion de muchos a muchos con el modelo User, ya que un rol 
   //lo pueden tener varios usuarios y un usuario puede tener varios roles
   public function users(){
        return $this->belongsToMany('App\User');

    }
}
