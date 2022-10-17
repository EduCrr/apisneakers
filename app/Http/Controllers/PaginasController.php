<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pagina;
use App\Models\PaginaIdioma;
use App\Models\Idioma;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class PaginasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'home', 'update', 'imagem' ] ] );  
    }

    public function home(Request $request, $controladora, $lng){
       
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $pagina = Pagina::where('controladora', $controladora)->first();

        if($pagina){
            $array = $pagina;
            $array['lng'] = $pagina->idiomas()->where('idioma_id', $lngId->id)->first();
            $array['path'] = url('content/display/');
        }else{
            $array['error'] = 'Nenhum conteúdo foi encontrado';
            return $array;
        }

        return $array; 
    }

    public function update(Request $request, $id, $lng){
        $array = ['error' => ''];   

        $rules = [
            'título' => 'required|max:255',
            'descrição' => 'required',
            'título_compartilhamento' => 'required|max:60',
            'descrição_compartilhamento' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        }     

        $titulo = $request->input('título');
        $descricao = $request->input('descrição');
        $titulo_compartilhamento = $request->input('título_compartilhamento');
        $descricao_compartilhamento = $request->input('descrição_compartilhamento');
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $paginaIdioma = PaginaIdioma::where('pagina_id', $id)->where('idioma_id', $lngId->id)->first();

        if($paginaIdioma){
            if($titulo){
                $paginaIdioma->titulo = $titulo;
                $paginaIdioma->save();
            }
            if($descricao){
                $paginaIdioma->descricao = $descricao;
                $paginaIdioma->save();
            }
            if($titulo_compartilhamento){
                $paginaIdioma->titulo_compartilhamento = $titulo_compartilhamento;
                $paginaIdioma->save();
            }
            if($descricao_compartilhamento){
                $paginaIdioma->descricao_compartilhamento = $descricao_compartilhamento;
                $paginaIdioma->save();
            }
        }else{
            $newPaginaIdioma = new PaginaIdioma();
            $newPaginaIdioma->titulo = $titulo;
            $newPaginaIdioma->descricao = $descricao;
            $newPaginaIdioma->titulo_compartilhamento = $titulo_compartilhamento;
            $newPaginaIdioma->descricao_compartilhamento = $descricao_compartilhamento;
            $newPaginaIdioma->pagina_id = $id;
            $newPaginaIdioma->idioma_id = $lngId->id;
            $newPaginaIdioma->criado = date('Y-m-d H:i:s');
            $newPaginaIdioma->save();
        }

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
            $extension = $request->file('imagem')->extension();


            if($imagem){
                File::delete(public_path("/content/display/".$pagina->imagem));
                $dest = public_path('content/display');
                $photoName = md5(time().rand(0,9999)).'.'.$extension;
        
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
