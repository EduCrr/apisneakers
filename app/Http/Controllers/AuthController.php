<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['create', 'login', 'unauthorized']]);
    }
    public function create(Request $request){
        //logado?
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(!$validator->fails()){

            $name = $request->input('name');
            $email = $request->input('email');
            $password = $request->input('password');

            //cadastro
            $emailExists = User::where('email', $email)->count();

            if($emailExists === 0){

                $hash = password_hash($password, PASSWORD_DEFAULT);

                $newUser = new User();
                $newUser->email = $email;
                $newUser->name = $name;
                $newUser->password = $hash;
                $newUser->save();

                //logar

                $token = auth()->attempt([
                    'email' => $email,
                    'password'=> $password
                ]);

                if(!$token){
                    $array['error'] = 'Ocorreu algum erro!';
                    return $array;
                }

                $info = auth()->user();
                $info['avatar'] = url('content/avatars/'.$info['avatar']);
                $array['data'] = $info;
                $array['token'] = $token;

            }else{
                $array['error'] = 'Email já cadastrado!';
                return $array;
            }

        }else{
            $array['error'] = 'Dados incorretos';
            return $array;
        }

        return $array;
    }

    //login

    public function login(Request $request){
        $array = ['error' => ''];

        $email = $request->input('email');
        $password = $request->input('password');
        
        $token = auth()->attempt([
            'email' => $email,
            'password'=> $password
        ]);

        if(!$token){
            $array['error'] = 'Email e/ou senha incorretos';
            return $array;
        }

        $info = auth()->user();
        $info['avatar'] = url('content/avatars/'.$info['avatar']);
        $array['data'] = $info;
        $array['token'] = $token;

        return $array;

    }

    public function logout(){
        auth()->logout();

        return ['error' => ''];
    }

    public function refresh(){
        $array = ['error' => ''];

        $token = auth()->refresh();

        $info = auth()->user();
        $info['avatar'] = url('content/avatars/'.$info['avatar']);
        $array['data'] = $info;
        $array['token'] = $token;

        return $array;
    }

    public function unauthorized(){

        return response()->json([
            'error' => 'Não autorizado'
        ], 401);

    }
}