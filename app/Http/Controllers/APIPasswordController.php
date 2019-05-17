<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\User;
use Hash;
use JWTAuth;
use Mail;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class APIPasswordController extends Controller
{
    // public function repassword(Request $request)
    // {        
    // 	$input = $request->only('email');
    // 	$input['password'] = Hash::make($input['password']);
    // 	User::create($input);
    //     return response()->json(['result'=>true]);
    // }


    private function getRandomCode(){
        // $an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-)(.:,;";
        $an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ._-abcdefghijklmnopqrstuvwxyz";
        $su = strlen($an) - 1;
        return substr($an, rand(0, $su), 1) .
                substr($an, rand(0, $su), 1) .
                substr($an, rand(0, $su), 1) .
                substr($an, rand(0, $su), 1) .
                substr($an, rand(0, $su), 1) .
                substr($an, rand(0, $su), 1);
    }
    
    public function resetpassword(Request $request)
    {
    	


        $validator = Validator::make($request->all(), [
            'email' => 'email'
        ]);


        if ($validator->fails()) {
            // return response()->json(['cod' => 'WS003', 'subCod' => 403, 'msg'=>trans('passwords.email'),'data'=>null, 'validation'=> ['cod' => '', 'subcod' => '', "msg" => '', "erros"=>$validator->errors()]]);


            return response()->json([
                'success'       => false,
                'tipomensaje'   => 'Danger',
                'mensaje'       => trans('passwords.email'),
                'data'          => $validator->errors()
                ]
            ,404);               

        }


        try{

            $input = $request->all();

            $user = User::where('email','=',$input['email'])->get()->first();
            $user->security_code = $this->getRandomCode();
            $user->save();

            $data = array(
                'codigo' => $user->security_code,
                'email' => $user->email,
                'nombre' => $user->nombre.' '.$user->apellido
            );

            if (!$token = JWTAuth::fromUser($user)) {                
                //return response()->json(['cod' => 'WS002', 'msg'=>trans('passwords.token')]);
                return response()->json([
                    'success'       => false,
                    'tipomensaje'   => 'Danger',
                    'mensaje'       => trans('passwords.token')
                    ]
                ,404);                      
            }

            // Mail::send('welcome', $data, function($msj) use ($data){
            //     $msj->subject(trans('passwords.code'));
            //     $msj->to($data['email']);
            // });
            
            //return response()->json(['cod' => 'WS001', 'subCod' => 200, 'msg'=>trans('passwords.sent'),'data'=>['token' => $token], 'validation'=> ['cod' => 'V001', 'subcod' => 200, "msg" => 'Validaciones correctas', "erros"=>$validator->errors()]]);
            return response()->json([
                'success'       => true,
                'tipomensaje'   => 'Success',
                'mensaje'       => trans('passwords.sent'),
                'data'          => [
                    'token'     => $token,
                    'errores'   => $validator->errors()
                    ]
                ]
            ,200);              

        }catch(\Exception $e){            
            // return response()->json(['cod' => 'WS003', 'subCod' => 500, 'msg'=>trans('passwords.user'),'data'=>null, 'validation'=> ['cod' => 'V001', 'subcod' => 200, "msg" => 'Validaciones correctas', "erros"=>$validator->errors()]]);
            return response()->json([
                'success'       => false,
                'tipomensaje'   => 'Success',
                'mensaje'       => trans('passwords.sent'),
                'data'          => ['errores' => $validator->errors()]
                ]
            ,500);              
        }
    }
    
    public function set_new_password(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'token'    => 'required',
            'codigo'   => 'required',
        ],[
            'required' => 'El campo :attribute es requerido.',
        ]);

        if ($validator->fails()) {
            // return response()->json(['cod' => 'WS003', 'msg'=>trans('passwords.email'), 'validation'=>$validator->errors()]);
            return response()->json([
                'success'       => false,
                'tipomensaje'   => 'Danger',
                'mensaje'       => trans('passwords.email'),
                'data'          => ['errores' => $validator->errors()]
                ]
            ,404);                   
        }


        $input = $request->all();
    	$user = JWTAuth::toUser($input['token']);

        if($user->security_code != $input['codigo'])
            //return response()->json(['cod' => 'WS002', 'msg'=>trans('passwords.codefailed')]);
            return response()->json([
                'success'       => false,
                'tipomensaje'   => 'Danger',
                'mensaje'       => trans('passwords.codefailed')
                ]
            ,404);             

        $user = User::find($user->id);
        $user->password =  Hash::make($input['password']);
        //$user->password = hash('sha256', $input['password']);
        $user->security_code = null;
        $user->save();

        // return response()->json(['cod' => 'WS001', 'msg'=>trans('passwords.reset')]);
        return response()->json([
            'success'       => false,
            'tipomensaje'   => 'Danger',
            'mensaje'       => trans('passwords.reset')
            ]
        ,404);             

    }

}    