<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/*  Lineas por defecto */
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
/*  Lineas por defecto */


/** JWT */
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@authenticate');
//obtenerListadoClientes
Route::post('obtenerListadoClientes', 'AuthController@obtenerListadoClientes');      

//Insertar Movil
Route::post('insertarMovil', 'AuthController@insertarMovil');

//Insertar Coordenada
Route::post('insertarCoordenada', 'AuthController@insertarCoordenada');   

// Route::get('open', 'DataController@open');

Route::group(['middleware' => ['jwt.verify']], function() {

    //Test Obtiene la informacion de le otra Base de Datos
    Route::get('otraDB', 'AuthController@otraDB');    

    //Obtiene la informacion del usuario autenticado
    Route::get('user', 'AuthController@obtenerUsuarioAutenticado');

    //Obtiene toda la informacion de los usuarios 
    Route::get('usuarios/{id?}', 'AuthController@obtenerUsuarios');

    
    //Actualiza informacion de un usuario

    //Activa Moviles
    Route::post('inactivarDispositivo', 'AuthController@inactivarDispositivo');

    //Inactiva Moviles
    Route::post('activarDispositivo', 'AuthController@activarDispositivo');

    //Obtiene el historial de  Coordenaadas por cliente y dispositivo
    Route::post('obtenercoordenadasmovilparamapeohistorico', 'AuthController@obtenerCoordenadasMovilParaMapeoHistorico');    

    //Obtiene las  Coordenaadas actuales de todos los dispositivos por cliente
    Route::post('obtenercoordenadasmovilparamapeoonline', 'AuthController@obtenerCoordenadasMovilParaMapeoOnline');    
    

    //Obtiene un el Listado de Moviles por Cliente
    Route::post('obtenerListadoMovilesPorCliente', 'AuthController@obtenerListadoMovilesPorCliente'); 


 

    // Faltantes



    Route::post('insertarCoordenadaPorLote', 'AuthController@insertarCoordenadaPorLote');  

    Route::post('refresh', 'AuthController@refresh');  
      
    Route::post('logout', 'AuthController@logout');
    
    // Route::get('closed', 'DataController@closed');
});


// Direcciones para el ws de reset password

//Devuelve el un token en caso de el usuario existir en base de datos
Route::post('resetpassword', 'APIPasswordController@resetpassword');
//Envia token para resetear password
Route::group(['middleware'=>['before'=>'jwt.auth']], function () {
    Route::post('set_new_password', 'APIPasswordController@set_new_password');
});  

// Route::group([
//     'prefix' => 'auth',
// ], function () {
//     Route::post('login', 'AuthController@login');
//     Route::post('logout', 'AuthController@logout');
//     Route::post('refresh', 'AuthController@refresh');
//     Route::post('me', 'AuthController@me');
//     Route::post('payload', 'AuthController@payload');
// });