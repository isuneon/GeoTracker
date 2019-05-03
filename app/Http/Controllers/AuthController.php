<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
/*JWT* */
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use Log;
use Carbon\Carbon;



class AuthController extends Controller
{
    
    public function authenticate(Request $request)
    {

        $validateRule = [
            'co_cli'   => 'required',
            'email'    => 'required | email',
            'password' => 'required'
        ];
        
        //Se verifica que los parametros sean los correctos
        //$compareParameter = count(array_diff_key($request->all(), $validateRule));
        //Log::info($compareParameter);

        // Validando con los requeridos y formatos
        // $validator = Validator::make($request->all(), $validateRule);                    
        // if ($validator->fails()) {
        //     return response()->json(['success' => false, 'mensaje'=>"Por favor todos los campos son requeridos.", 'validation'=>$validator->errors()]);            
        // }

        $token = null;
        
        try {
            //Creacion de token de usuario
            if (!$token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password])) {            

            // $credentials = $request->only('email', 'password');
            // if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'succes' => false, 
                    'tipomensaje' => 'Alert', 
                    'mensaje'=>'Autenticacion Fallida, por favor verifique los datos ingresados.'
                ], 401);
            }
            else{                
                /****************************************************/
                //     Verificamos el status del Usuario 
                /****************************************************/                
                //$user = JWTAuth::toUser($token);
                $user = auth()->user();
                if ($user->borrado == 1){
                    return response()->json([
                        'succes' => false, 
                        'tipomensaje' => 'Alert', 
                        'mensaje'=>'Usuario inactivo.'
                    ], 401);
                };
                /****************************************************/

                /****************************************************/
                //     Obtenemos datos de configuracion de su empresa
                /****************************************************/                                                
                $co_cli_Logueado = $user->co_cli;
                $lcQuery  = 'CALL obtenerConfiguracionByCliente (?)';
                //$usuarioLogin =\DB::select('call ObtenerUsuarioLogin(?,?,?,?,?)',array($request->co_cli,$request->email,$password,$mensaje, $mensaje));                               
                $configuracionCliente = \DB::select($lcQuery,array($request->co_cli));                
                Log::info($configuracionCliente);
               // $fechaVencimientoLicencia = $configuracionCliente[0]->vencimiento_Licencia;
                //$diasParaVencimientoLicencia = $configuracionCliente[0]->dias_vencerLicencia;
                //$licenciaActiva = $configuracionCliente[0]->licenciaActiva;
                /****************************************************/


                /****************************************************/
                //     Verificamos el status de la licencia
                /****************************************************/
                // if ($licenciaActiva == 0){
                //     return response()->json([
                //         'succes' => false, 
                //         'tipomensaje' => 'Alert', 
                //         'mensaje'=>'Licencia Inactiva.'
                //     ], 401);
                // };                
                /****************************************************/

                // $cliente = \DB::select(
                //     'select a.*,b.* FROM admin_config                where a.borrado = 0 and b.borrado = 0 and a.email = :email'
                //     ,['email' => $token['user_email']]);                

                
                /****************************************************/                
                //Se obtienen los roles y permissions
                /****************************************************/                                
                $user->roles;
                Log::info($user->roles);
                foreach($user->roles as $rol){
                    $rol->permissions;   
                }                

                return response()->json([
                    'success'       => true,
                    'tipomensaje'   => 'Success.',
                    'mensaje'       => 'Autenticado.',              
                    'data'       => [
                        'token' => $token,
                        'user' => $user]
                    ]
                ,200);
                // return response()->json([
                //     'success'       => true,
                //     'tipomensaje'   => 'Success.',
                //     'mensaje'       => 'Autenticado.',
                //     'licencia'      =>[
                //         'licenciaActiva' => $licenciaActiva,
                //         'diasParaVencimiento' => $diasParaVencimientoLicencia,
                //         'vencimiento' => $fechaVencimientoLicencia                        
                //     ],                   
                //     'data'       => [
                //         'token' => $token,
                //         'user' => $user]
                //     ]
                // ,200);
                                 
            }
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'false'  , 
                'mensaje'=>'Imposible Crear el token.'
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            // 'password' => 'required|string|min:6|confirmed',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }



    /*
        public function show($id)
    {
        $product = Product::find($id);


        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }


        return $this->sendResponse($product->toArray(), 'Product retrieved successfully.');
    }
     */
    public function obtenerUsuarios(Request $request, $id=null)
    {
        try {
            //if (!$user = JWTAuth::authenticate($request->token)) {
                //$id = $request->$id;
                $cliente = \DB::select('select a.* FROM users a');
                return response()->json([
                    'success' => true,
                    'users' => $cliente,
                    'id' => $id
                    ]);
        } 
        catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }
    }

    public function obtenerUsuarioAutenticado(Request $request)
    {
     
        try {
            // $this->validate($request, [
            //     'token' => 'required'
            // ]);               

            //$user = JWTAuth::authenticate($request->token);
            

            // if (!$user = JWTAuth::parseToken()->authenticate()) {
            //     return response()->json(['user_not_found'], 404);
            // }

            if (!$user = JWTAuth::authenticate($request->token)) {
                return response()->json(['user_not_found'], 404);
            }
            else{
                return response()->json([
                    'success' => true,
                    'user' => $user]);
            }            

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }
        // finally{
        //     return response()->json([
        //         'success' => true,
        //         'user' => $user]);
        // }

 
     
        // return response()->json(compact('user'));
    }


    /***************************************************************/
    
    public function obtenerCoordenadasMovilParaMapeoHistorico(Request $request)
    {
     
        try {
            // $this->validate($request, [
            //     'token' => 'required'
            // ]);               

            //$user = JWTAuth::authenticate($request->token);
            

            // if (!$user = JWTAuth::parseToken()->authenticate()) {
            //     return response()->json(['user_not_found'], 404);
            // }

            if (!$user = JWTAuth::authenticate($request->token)) {
                return response()->json(['user_not_found'], 404);
            }
            else{


                /**/
                if (is_null($request->pCo_cli) || empty($request->pCo_cli)) {
                    return response()->json([
                        'success'       => false,
                        'tipomensaje'   => 'Danger',
                        'mensaje'       => 'co_cli no puede estar vacio'
                        ]);                   
                }
        
                if (is_null($request->pFechaHoraDesde) || empty($request->pFechaHoraDesde)) {
                    return response()->json([
                        'success'       => false,
                        'tipomensaje'   => 'Danger',
                        'mensaje'       => 'El identificador Del Dispositivo no puede estar vacio'
                        ]);                   
                }
        
                if (is_null($request->pFechaHoraHasta) || empty($request->pFechaHoraHasta)) {
                    return response()->json([
                        'success'       => false,
                        'tipomensaje'   => 'Danger',
                        'mensaje'       => 'latitud no puede estar vacio'
                        ]);                   
                }      
                
                if (is_null($request->pIdMovil) || empty($request->pIdMovil)) {
                    return response()->json([
                        'success'       => false,
                        'tipomensaje'   => 'Danger',
                        'mensaje'       => 'longitud no puede estar vacio'
                        ]);                   
                }
                     
        
                // if (is_null($request->velocidad) || empty($request->velocidad)) {
                //     $velocidad  = $request->velocidad;                 
                // }
        
                // if (is_null($request->altura) || empty($request->altura)) {
                //     $altura  = $request->altura;                  
                // }
                // if (is_null($request->orientacion) || empty($request->orientacion)) {
                //     $orientacion  = $request->orientacion;                   
                // }  
    
                $lcQuery  = 'CALL ObtenerCoordenadasMovilParaMapeoHistorico (?,?,?,?)';
                // Log::info(Carbon::parse($request->pFechaHoraDesde));
                // Log::info(Carbon::parse($request->pFechaHoraHasta));
                // Log::info($request->pFechaHoraDesde);
                // Log::info($request->pFechaHoraHasta);

                // Log::info($request->pCo_cli);
                // Log::info($request->pIdMovil);

                try {
                    //code...
                    $respuesta = \DB::select(
                        $lcQuery,
                            array(
                                 $request->pCo_cli
                                ,$request->pFechaHoraDesde
                                ,$request->pFechaHoraHasta                            
                                ,$request->pIdMovil                  
                                )
                    );                      
                } catch (\Throwable $th) {
                    return response()->json([
                        'success'       => 0,
                        'tipomensaje'   => 'Danger',
                        'mensaje'       => 'Excepcion no controlada',
                        'data'          => $th]);  
                }
                           
                /**/
                // $now = CarbonImmutable::createFromDate(2018, 3, 15);
               // printf("Now: %s", Carbon::now());
               // $now =   Carbon::parse('2019/06/2');
               // $now2 = $now->isoFormat('YYYY-MM-DD'); // 1/3/19 18:33
                //$now = Carbon::now();
                return response()->json([
                    'success'       => 1,
                    'tipomensaje'   => 'Success',
                    'mensaje'       => 'Listado de usaurios para asociar con el telefono',
                    'data'          => $respuesta]);                                
            }            

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }
        // finally{
        //     return response()->json([
        //         'success' => true,
        //         'user' => $user]);
        // }

 
     
        // return response()->json(compact('user'));
    }

    public function obtenerCoordenadasMovilParaMapeoOnline(Request $request)
    {
        try {    

            if (!$user = JWTAuth::authenticate($request->token)) {
                return response()->json(['user_not_found'], 404);
            }
            else{


                /**/
                if (is_null($request->pCo_cli) || empty($request->pCo_cli)) {
                    return response()->json([
                        'success'       => false,
                        'tipomensaje'   => 'Danger',
                        'mensaje'       => 'co_cli no puede estar vacio'
                        ]);                   
                }
        
                          
    
                $lcQuery  = 'CALL ObtenerCoordenadasMovilParaMapeoOnline (?)';
             
                try {
                    //code...
                    $respuesta = \DB::select(
                        $lcQuery,
                            array(
                                 $request->pCo_cli               
                                )
                    );                      
                } catch (\Throwable $th) {
                    return response()->json([
                        'success'       => 0,
                        'tipomensaje'   => 'Danger',
                        'mensaje'       => 'Excepcion no controlada',
                        'data'          => $th]);  
                }
                   
                return response()->json([
                    'success'       => 1,
                    'tipomensaje'   => 'Success',
                    'mensaje'       => 'Listado de usaurios para asociar con el telefono',
                    'data'          => $respuesta]);                                
            }            

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }           
    }   
    
    public function inactivarDispositivo(Request $request)
    {
        // {  
        //     "1": "123",
        //     "2": "432",
        //     "3": "1023" 
        // }

        // moviles = { "1": "123","2": "432", "3": "1023"}
        // [{"name":"Jonathan Suh","gender":"male"},{"name":"William Philbin","gender":"male"},{"name":"Allison McKinnery","gender":"female"}]        
        try {
            // $this->validate($request, [
            //     'token' => 'required'
            // ]);               

            //$user = JWTAuth::authenticate($request->token);
            

            // if (!$user = JWTAuth::parseToken()->authenticate()) {
            //     return response()->json(['user_not_found'], 404);
            // }

            if (!$user = JWTAuth::authenticate($request->token)) {
                return response()->json(['user_not_found'], 404);
            }
            else{
                $data =json_decode($request->moviles, true);
                //Log::info($data[0]["1"]);


                // $arrSku = array('612552892' => array('quantity' => 1), '625512336' => array('quantity' => 10) );

                // $arrNewSku = array();
                // $incI = 0;
                // foreach($arrSku AS $arrKey => $arrData){
                //     $arrNewSku[$incI]['sku_id'] = $arrKey;
                //     $arrNewSku[$incI]['quantity'] = $arrData['quantity'];
                //     $incI++;
                // }                
                
                

                $lcQuery  = 'CALL InactivarDispositivo (?)';                


                try {
                    //code...
                    foreach($data AS $indice => $valor){
                        //Log::info($indice);
                        //Log::info($valor);      
                        $respuesta = \DB::select(
                            $lcQuery,
                                array(
                                     $valor              
                                    )
                        );                                         
                    }    

                } catch (\Throwable $th) {
                    return response()->json([
                        'success'       => 0,
                        'tipomensaje'   => 'Danger',
                        'mensaje'       => 'Excepcion no controlada',
                        'data'          => $th]);  
                }
                                
                return response()->json([
                    'success' => 1,
                    'tipomensaje'   => 'Success',
                    'mensaje'       => 'Dispositivo Inactivado con Exito',                    
                    'data' => $respuesta]);
            }            

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }
    }    
    
    public function activarDispositivo(Request $request)
    {
        // moviles = { "1": "123","2": "432", "3": "1023"}        
        try {

            if (!$user = JWTAuth::authenticate($request->token)) {
                return response()->json(['user_not_found'], 404);
            }
            else{
                $data =json_decode($request->moviles, true);
 
                $lcQuery  = 'CALL ActivarDispositivo (?)';                


                try {
                    //code...
                    foreach($data AS $indice => $valor){
                        //Log::info($indice);
                        //Log::info($valor);      
                        $respuesta = \DB::select(
                            $lcQuery,
                                array(
                                     $valor              
                                    )
                        );                                         
                    }    

                } catch (\Throwable $th) {
                    return response()->json([
                        'success'       => 0,
                        'tipomensaje'   => 'Danger',
                        'mensaje'       => 'Excepcion no controlada',
                        'data'          => $th]);  
                }
                                
                return response()->json([
                    'success' => true,
                    'user' => $respuesta]);
            }            

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }
    } 
    
    public function obtenerListadoMovilesPorCliente(Request $request)
    {
     
        try {
 
            if (!$user = JWTAuth::authenticate($request->token)) {
                return response()->json(['user_not_found'], 404);
            }            
            try {
                //code...
                $respuesta = \DB::select('select co_cli FROM vClientes a');  
    
                $lcQuery  = 'CALL ObtenerListadoMoviles (?)';                

                $respuesta = \DB::select(
                    $lcQuery,
                        array(
                            $request->pCo_cli              
                            )
                ); 
                  
            } catch (\Throwable $th) {
                return response()->json([
                    'success'       => 0,
                    'tipomensaje'   => 'Danger',
                    'mensaje'       => 'Excepcion no controlada',
                    'data'          => $th]);  
            }
                        
            // $respuesta = \DB::select($lcQuery);                
            // Log::info($respuesta);  
            return response()->json([
                'success'       => 1,
                'tipomensaje'   => 'Success',
                'mensaje'       => 'Listado de usaurios para asociar con el telefono',
                'data'          => $respuesta]);              

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }
        // finally{
        //     return response()->json([
        //         'success' => true,
        //         'user' => $user]);
        // }

 
     
        // return response()->json(compact('user'));
    }   
   
    public function insertarCoordenadaPorLote(Request $request)
    {
        $respuesta = '';
        // {  
        //     "1": "123",
        //     "2": "432",
        //     "3": "1023" 
        // }
        
        //{"1": { "latitud": "b","longitud": "d","fecha": "f"},"2": { "latitud": "2a","longitud": "2b","fecha": "2c"},"3": { "latitud": "3a","longitud": "3b","fecha": "3c"}}
        // {"1": { "latitud": "b","longitud": "d","fecha": "f"}}
        // moviles = { "1": "123","2": "432", "3": "1023"}
        // [{"name":"Jonathan Suh","gender":"male"},{"name":"William Philbin","gender":"male"},{"name":"Allison McKinnery","gender":"female"}]        
        try {
            // $this->validate($request, [
            //     'token' => 'required'
            // ]);               

            //$user = JWTAuth::authenticate($request->token);
            

            // if (!$user = JWTAuth::parseToken()->authenticate()) {
            //     return response()->json(['user_not_found'], 404);
            // }

            if (!$user = JWTAuth::authenticate($request->token)) {
                return response()->json(['user_not_found'], 404);
            }
            else{
                $jsonIN =json_decode($request->moviles, true);
                //Log::info($data);


                // if (is_null($request->co_cli) || empty($request->co_cli)) {
                //     return response()->json([
                //         'success'       => false,
                //         'tipomensaje'   => 'Danger',
                //         'mensaje'       => 'co_cli no puede estar vacio'
                //         ]);                   
                // }     

                try {
                    //code...


                    /*

        $lcQuery  = 'CALL InsertarCoordenada (?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $respuesta = \DB::select(
            $lcQuery,
                array(
                     $request->co_cli
                    ,$request->idDispositivo
                    ,$request->latitud
                    ,$request->longitud
                    ,$request->fecha
                    ,$velocidad  
                    ,$altura
                    ,$precisionMovil  
                    ,$orientacion  
                    ,$odometro  
                    ,$descripcion
                    ,$detencion                    
                    ,$fechaAnterior                    
                    )
        );
                    */
                    $lcQuery  = 'CALL InsertarCoordenadaPorLote (?,?,?,?,?,?,?,?,?,?,?,?,?)';
                             
                    foreach($jsonIN AS $idMovil => $data){
                        // Log::info($idMovil);
                        // Log::info($data); 
                        // Log::info($data["latitud"]);      
                        // Log::info($data["longitud"]); 
                        // Log::info($data["fecha"]);
                      
                        if (is_null($idMovil) || empty($idMovil)) {
                           //Log::info("InsertarCoordenadaPorLote Numero de Movil Vacio :   ". Carbon::now());
                           Log::info("InsertarCoordenadaPorLote Numero de Movil Vacio :   ");
                           //next;               
                           return response()->json([
                            'success'       => 0,
                            'tipomensaje'   => 'Danger',
                            'mensaje'       => 'latitud no puede estar vacio'
                            ]);                           
                        }
        
                        if (is_null($data["latitud"]) || empty($data["latitud"])) {
                            //Log::info("InsertarCoordenadaPorLote latitud no puede estar vacio :   ". "Movil : " .$idMovil." Fecha: " . Carbon::now());
                            Log::info("InsertarCoordenadaPorLote latitud no puede estar vacio :   ". "Movil : " .$idMovil);
                            //next; 
                            return response()->json([
                                'success'       => 0,
                                'tipomensaje'   => 'Danger',
                                'mensaje'       => 'latitud no puede estar vacio'
                                ]);                   
                        }      
        
                        if (is_null($data["longitud"]) || empty($data["longitud"])) {
                            Log::info("InsertarCoordenadaPorLote longitud no puede estar vacio :   ". "Movil : " .$idMovil);
                           // next;
                            return response()->json([
                                'success'       => 0,
                                'tipomensaje'   => 'Danger',
                                'mensaje'       => 'longitud no puede estar vacio'
                                ]);                   
                        }
        
                        if (is_null($data["fecha"]) || empty($data["fecha"])) {
                            Log::info("InsertarCoordenadaPorLote fecha no puede estar vacio :   ". "Movil : " .$idMovil);
                           // next;
                            return response()->json([
                                'success'       => 0,
                                'tipomensaje'   => 'Danger',
                                'mensaje'       => 'fecha de usuario no puede estar vacio'
                                ]);                   
                        }      

                        // $respuesta = $respuesta + \DB::select(
                        //     $lcQuery,
                        //         array(
                        //              $idMovil
                        //             ,$data["latitud"]
                        //             ,$data["longitud"]
                        //             ,$data["fecha"]              
                        //             )
                        // );                                         
                }    

                } catch (\Throwable $th) {
                    return response()->json([
                        'success'       => 0,
                        'tipomensaje'   => 'Danger',
                        'mensaje'       => 'Excepcion no controlada',
                        'data'          => $th]);  
                }
                                
                // return response()->json([
                //     'success' => 1,
                //     'tipomensaje'   => 'Success',
                //     'mensaje'       => 'Dispositivo Inactivado con Exito',                    
                //     'data' => $jsonIN]);

                return response()->json([
                    'success' => 1,
                    'tipomensaje'   => 'Success',
                    'mensaje'       => 'Dispositivo Inactivado con Exito',                    
                    'data' => $jsonIN]);                    
            }            

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }
    }      
    
    /***************************************************************/
    /*                          Listas                             */
    /***************************************************************/
    public function obtenerListadoClientes(Request $request)
    {

        try {
            //code...
            $respuesta = \DB::select('select co_cli FROM vClientes a');  

        } catch (\Throwable $th) {
            return response()->json([
                'success'       => 0,
                'tipomensaje'   => 'Danger',
                'mensaje'       => 'Excepcion no controlada',
                'data'          => $th]);  
        }
                    
        // $respuesta = \DB::select($lcQuery);                
        // Log::info($respuesta);  
        return response()->json([
            'success'       => 1,
            'tipomensaje'   => 'Success',
            'mensaje'       => 'Listado de usaurios para asociar con el telefono',
            'data'          => $respuesta]);                                     
    }     
    public function insertarMovil(Request $request)
    {
                
        if (is_null($request->idDispositivo) || empty($request->idDispositivo)) {
            return response()->json([
                'success'       => false,
                'tipomensaje'   => 'Danger',
                'mensaje'       => 'El identificador Del Dispositivo no puede estar vacio'
                ]);                   
        }

        if (is_null($request->co_cli) || empty($request->co_cli)) {
            return response()->json([
                'success'       => false,
                'tipomensaje'   => 'Danger',
                'mensaje'       => 'El codigo de usuario no puede estar vacio'
                ]);                   
        }            



        $lcQuery  = 'CALL InsertarMovil (?,?,?)';
        //$usuarioLogin =\DB::select('call ObtenerUsuarioLogin(?,?,?,?,?)',array($request->co_cli,$request->email,$password,$mensaje, $mensaje));                               
        $idTipoMovil  = 1; //  Tipo Movil  = CELULAR ANDROID
        $respuesta = \DB::select($lcQuery,array($request->idDispositivo,$idTipoMovil,$request->co_cli));                
        // Log::info($respuesta);  
        
        if (is_null($respuesta) || empty($respuesta) ) {
            # code...
            return response()->json([
                'success'       => 0,
                'tipomensaje'   => 'BackEnd',
                'mensaje'       => 'Ocurrio una Excpcion no controlada por favor contactar a soporte',
                'idMovil'          => 0]);                    
        } else {
            # code...
            // return response()->json([
            //    'data' => $respuesta]);                    
            return response()->json([
                'success'       => $respuesta[0]->succes,
                'tipomensaje'   => 'Success',
                'mensaje'       => $respuesta[0]->mensaje,
                'idMovil'          => $respuesta[0]->resultado]);                      
        }                                
    } 
    
    public function insertarCoordenada(Request $request)
    {
     
        try {
            //code...
            $velocidad  = NULL;             
            $altura  = NULL;     
            $orientacion  = NULL;     
            $odometro  = NULL;     
            $descripcion  = NULL;     
            $detencion  = NULL; 
            $fecha = NULL;      
            $fechaAnterior  = NULL;    
            $precisionMovil  = NULL;  
            
           // Log::info($request->velocidad);
            if (is_null($request->co_cli) || empty($request->co_cli)) {
                return response()->json([
                    'success'       => false,
                    'tipomensaje'   => 'Danger',
                    'mensaje'       => 'co_cli no puede estar vacio'
                    ]);                   
            }
    
            if (is_null($request->idDispositivo) || empty($request->idDispositivo)) {
                return response()->json([
                    'success'       => false,
                    'tipomensaje'   => 'Danger',
                    'mensaje'       => 'El identificador Del Dispositivo no puede estar vacio'
                    ]);                   
            }
    
            if (is_null($request->latitud) || empty($request->latitud)) {
                return response()->json([
                    'success'       => false,
                    'tipomensaje'   => 'Danger',
                    'mensaje'       => 'latitud no puede estar vacio'
                    ]);                   
            }      
            
            if (is_null($request->longitud) || empty($request->longitud)) {
                return response()->json([
                    'success'       => false,
                    'tipomensaje'   => 'Danger',
                    'mensaje'       => 'longitud no puede estar vacio'
                    ]);                   
            }
            
            if (is_null($request->fecha) || empty($request->fecha)) {
                return response()->json([
                    'success'       => false,
                    'tipomensaje'   => 'Danger',
                    'mensaje'       => 'fecha de usuario no puede estar vacio'
                    ]);                   
            }           
    
            if (!is_null($request->velocidad) || !empty($request->velocidad)) {
                // $velocidad  = $request->velocidad;    
                Log::info($request->velocidad);   
                
            }
    
            if (!is_null($request->altura) || !empty($request->altura)) {
                $altura  = $request->altura;                  
            }
            if (!is_null($request->orientacion) || !empty($request->orientacion)) {
                $orientacion  = $request->orientacion;                   
            }  
    
            if (!is_null($request->odometro) || !empty($request->odometro)) {
                $odometro  = $request->odometro;                   
            }    
    
            if (!is_null($request->descripcion) || !empty($request->descripcion)) {
                $descripcion  = $request->descripcion;                   
            } 
            if (!is_null($request->detencion) || !empty($request->detencion)) {
                $detencion  = $request->detencion;                   
            } 
            if (!is_null($request->fecha) || !empty($request->fecha)) {
                $fecha  = $request->fecha;                   
            }            
            if (!is_null($request->fechaAnterior) || !empty($request->fechaAnterior)) {
                $fechaAnterior  = $request->fechaAnterior;                   
            }                             
            if (!is_null($request->precisionMovil) || !empty($request->precisionMovil)) {
                $precisionMovil  = $request->precisionMovil;                  
            }  
                  
            $lcQuery  = 'CALL InsertarCoordenada (?,?,?,?,?,?,?,?,?,?,?,?,?)';
            $respuesta = \DB::select(
                $lcQuery,
                    array(
                         $request->co_cli
                        ,$request->idDispositivo
                        ,$request->latitud
                        ,$request->longitud                        
                        ,$velocidad  
                        ,$altura
                        ,$precisionMovil  
                        ,$orientacion  
                        ,$odometro  
                        ,$descripcion
                        ,$detencion         
                        ,$fecha           
                        ,$fechaAnterior                    
                        )
            );            
        } catch (\Throwable $th) {
            return response()->json([
                'success'       => 0,
                'tipomensaje'   => 'Danger',
                'mensaje'       => 'Excepcion no controlada',
                'data'          => $th]);  
        }


        // Log::info($respuesta);  
        
        // if (is_null($respuesta) || empty($respuesta) ) {
        //     # code...
        //     return response()->json([
        //         'success'       => 0,
        //         'tipomensaje'   => 'BackEnd',
        //         'mensaje'       => 'Ocurrio una Excpcion no controlada por favor contactar a soporte',
        //         'idMovil'       => 0]);                    
        // } else {
            # code...
            return response()->json([
               'data' => $respuesta]);                    
            // return response()->json([
            //     'success'       => $respuesta[0]->succes,
            //     'tipomensaje'   => 'Success',
            //     'mensaje'       => $respuesta[0]->mensaje,
            //     'idMovil'       => $respuesta[0]->resultado]);                      
        // }  
    }     
    /***************************************************************/
    
    // public function logout()
    // {
    //     auth()->logout();
    //     return response()->json(['message' => 'Successfully logged out']);
    // }

    public function logout(Request $request)
    {
    	$input = $request->all();
        try{
            if (JWTAuth::invalidate($input['token'])) {
                return response()->json(['success' => true, 'message'=>'Sesion Caducada']);
            }
                return response()->json(['success' => false  , 'message'=>'Error al cerrar sesion']);

        }catch (JWTException $exception){
                return response()->json(['success' => false  , 'message'=>'Token no existe']);
        }

        return response()->json(['success' => false  , 'message'=>'usuario no existe']);     
        // $this->validate($request, [
        //     'token' => 'required'
        // ]);

        // try {
        //     //JWTAuth::invalidate(JWTAuth::getToken());
        //     //auth()->logout();
        //     JWTAuth::invalidate($request->token);
 
            
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'User logged out successfully'
        //     ]);
        // } catch (JWTException $exception) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Sorry, the user cannot be logged out'
        //     ], 500);
        // }

        // auth()->logout();
        // return response()->json(['message' => 'Successfully logged out']);
    }    
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
        // return response()->json([
        //     'access_token' => $token,
        //     'token_type' => 'bearer',
        //     'expires_in' => auth()->factory()->getTTL() * 60,
        // ]);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /************************************************************************************/
    //      Metodos para realizar conexiones con la DB del Usuario
    /************************************************************************************/
    public static function getDatabaseConfigArray()
    {
        $connection = config('database.default');

        return [
            'host' => config("database.connections.$connection.host"),
            'port' => config("database.connections.$connection.port"),
            'username' => config("database.connections.$connection.username"),
            'password' => config("database.connections.$connection.password"),
            'database' => config("database.connections.$connection.database"),
        ];
    }

    public static function setDatabaseFromClient($client)
    {
        \DB::disconnect();
        self::setDatabase($client->dw_server, $client->dw_port, $client->dw_user, $client->dw_pass, $client->dw_dbname,true);
    }

    public static function setDatabase($host, $port, $username, $password, $database_name)
    {
        $connection = config('database.default');

        config([
            "database.connections.$connection.database" => $database_name,
            "database.connections.$connection.host" => $host,
            "database.connections.$connection.port" => $port,
            "database.connections.$connection.username" => $username,
            "database.connections.$connection.password" => $password,
        ]);
    }

    public static function setDatabaseFromArray($array)
    {
        self::setDatabase($array['host'], $array['port'], $array['username'], $array['password'], $array['database'], true);
        \DB::disconnect();
    }    
    /************************************************************************************/
    /************************************************************************************/
    public function otraDB(Request $request){
        $cliente = \DB::select('SELECT dw_server ,dw_dbname ,dw_user ,dw_pass ,dw_port FROM admin_config');

        $defaultConnection = $this->getDatabaseConfigArray();
        //Log::info($defaultConnection);
        $this->setDatabaseFromClient($cliente[0]);
        //Log::info($defaultConnection);
        
        $blogs = \DB::select('SELECT * FROM productos');
        
        $this->setDatabaseFromArray($defaultConnection);
        $listado = [];
        $i = 0;
        foreach ($blogs as $blog) {
            $listado[$i] = [$blog->co_pro,$blog->nombre,$blog->descripcion];
            $i++;
        }
        $clientea = \DB::select('SELECT dw_server ,dw_dbname ,dw_user ,dw_pass ,dw_port FROM admin_config');
        return response()->json($listado);
    }
}