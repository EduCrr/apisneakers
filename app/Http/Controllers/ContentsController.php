<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conteudo;
use App\Models\Idioma;
use App\Models\ConteudoIdioma;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class ContentsController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'home', 'update', 'contentId' ] ] );  
    }

    public function home(Request $request, $controladora, $lng){
       
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $content = Conteudo::where('controladora', $controladora)->get();

        if($content){
            foreach($content as $key => $item){
                $content[$key]['parametros'] = $item->parametros;
                $content[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['content'] = $content;
            $array['path'] = url('content/display/');
        }else{
            $array['error'] = 'Nenhum conteúdo foi encontrado';
            return $array;
        }
        
        return $array; 
    }

     public function contentId(Request $request, $id, $lng){
       
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $content = Conteudo::where('id', $id)->get();

        if($content){
            foreach($content as $key => $item){
                $content[$key]['parametros'] = $item->parametros;
                $content[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['content'] = $content;
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
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $title = $request->input('título');
        $description = $request->input('descrição');
        $content = ConteudoIdioma::where('conteudo_id', $id)->where('idioma_id', $lngId->id)->first();

        if($content){
            if($title){
                $content->titulo = $title;
                $content->save();
            }
            if($description){
                $content->descricao = $description;
                $content->save();
            }
            
        }else{
            $newContentIdioma = new ConteudoIdioma();
            $newContentIdioma->titulo = $title;
            $newContentIdioma->descricao = $description;
            $newContentIdioma->conteudo_id = $id;
            $newContentIdioma->idioma_id = $lngId->id;
            $newContentIdioma->criado = date('Y-m-d H:i:s');
            $newContentIdioma->save();
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
            $content = Conteudo::find($id);
            $extension = $request->file('imagem')->extension();
            if($imagem){
                File::delete(public_path("/content/display/".$content->imagem));
                $dest = public_path('content/display');
                $photoName = md5(time().rand(0,9999)).'.'.$extension;
        
                $img = Image::make($imagem->getRealPath());
                $img->fit($content->largura_imagem,$content->altura_imagem,)->save($dest.'/'.$photoName);

                $content->imagem = $photoName;
                $content->save();
                
            }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;

    }

     public function imagemResponsive(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'imagem' =>  'required|image|mimes:jpeg,png,jpg,svg',
        ]);

        if(!$validator->fails()){

            $imagem = $request->file('imagem');
            $content = Conteudo::find($id);
            $extension = $request->file('imagem')->extension();


            if($imagem){
                File::delete(public_path("/content/display/".$content->imagem_responsive));
                $dest = public_path('content/display');
                $photoName = md5(time().rand(0,9999)).'.'.$extension;
        
                $img = Image::make($imagem->getRealPath());
                $img->fit($content->largura_imagem_responsive,$content->altura_imagem_responsive,)->save($dest.'/'.$photoName);

                $content->imagem_responsive = $photoName;
                $content->save();
                
            }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;

    }

}
