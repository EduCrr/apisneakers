<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pagina;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class PaginasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'home' ] ] );  
    }

    public function home(Request $request, $controladora){
       
        $array = ['error' => ''];
        $pagina = Pagina::where('controladora', $controladora)->get();

        if($pagina){
            $array = $pagina;
            $array['path'] = url('content/display/');
        }else{
            $array['error'] = 'Nenhum conteúdo foi encontrado';
            return $array;
        }
        
        return $array; 
    }

    public function update(Request $request, $id){
        $array = ['error' => ''];   

        $rules = [
            'título' => 'required',
            'descrição' => 'required',
            'título_compartilhamento' => 'required',
            'descrição_compartilhamento' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        }     

        $titulo = $request->input('título');
        $descricao = $request->input('descrição');
        $titulo_compartilhamento = $request->input('título compartilhamento');
        $descricao_compartilhamento = $request->input('descrição compartilhamento');
        $pagina = Pagina::find($id);

            if($titulo){
                $pagina->titulo = $titulo;
            }

            if($descricao){
                $pagina->descricao = $descricao;
            }

            if($titulo_compartilhamento){
                $pagina->titulo_compartilhamento = $titulo_compartilhamento;
            }

            if($descricao_compartilhamento){
                $pagina->descricao_compartilhamento = $descricao_compartilhamento;
            }

            $pagina->save();

        return $array;

    }

    public function imagem(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'imagem' =>  'required|image|mimes:jpeg,png,jpg,svg',
        ]);

        if(!$validator->fails()){

            $imagem = $request->file('imagem');
            $pagina = Pagina::find($id);

            if($imagem){
                File::delete(public_path("/content/display/".$pagina->imagem));
                $dest = public_path('content/display');
                $photoName = md5(time().rand(0,9999)).'.jpg';
        
                $img = Image::make($imagem->getRealPath());
                $img->fit(1280, 720)->save($dest.'/'.$photoName);

                $pagina->imagem = $photoName;
                $pagina->save();
                
            }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;

    }

}
