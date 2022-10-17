<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\Teste;
use File;

class EmailsController extends Controller
{
    public function email(Request $request){

        $array = ['error' => ''];
        $array = ['success' => ''];

        $validator = Validator::make($request->all(), [
            'nome' => 'required',
            'email' => 'required|email',
            'mensagem' => 'required',

            
        ]);

        if(!$validator->fails()){
            
            $nome = $request->input('nome');
            $email = $request->input('email');
            $mensagem = $request->input('mensagem');
            $att = $request->file('att.*');

            $path = public_path('uploads');
            $files = [];

            if($att){

                foreach($att as $key => $item){
                   
                    if(!File::exists($path)) {
                        File::makeDirectory($path, $mode = 0777, true, true);
                    }
                    $title = md5(time().rand(0,9999)).'.'.$item->getClientOriginalExtension();
                   // $title = $key.time().'.'.$item->getClientOriginalExtension();

                    $item->move($path, $title);

                    $filename = $path.'/'.$title;
                
                    $files[$key] = $filename;

                }
            }
            
            $data["email"] = $email;
            $data["title"] = $nome;
            $data["mensagem"] = $mensagem;

            Mail::send('email.Teste', $data, function($message)use($data, $files) {
                $message->to('dudu1.6@hotmail.com')
                        ->subject('Email enviado pelo localhost');
                foreach ($files as $file){
                    $message->attach($file);
                }
            });

            $array['success'] = 'Email enviado!';
            return $array;

        }else {
            $array['error'] = 'Email não pode ser enviado!';
            return $array;
        }
        
        return $array;
    }
}

//https://web-tuts.com/how-to-send-email-with-attachment-in-laravel-8/