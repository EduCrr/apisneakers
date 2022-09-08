<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class ContentsController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'home' ] ] );  
    }

    public function home(Request $request, $controladora){
       
        $array = ['error' => ''];
        $content = Content::where('controladora', $controladora)->get();

        if($content){
            foreach($content as $key => $item){
                $content[$key]['parametros'] = $item->parametros;
            }
            $array['content'] = $content;
            $array['path'] = url('content/display/');
        }else{
            $array['error'] = 'Nenhum conteúdo foi encontrado';
            return $array;
        }
        
        return $array; 
    }

    public function update(Request $request, $id){
        $array = ['error' => ''];
        
        $title = $request->input('título');
        $description = $request->input('descrição');
        $content = Content::find($id);

            if($title){
                $content->title = $title;
            }

            if($description){
                $content->description = $description;
            }

            $content->save();

        return $array;

    }

    public function imagem(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'imagem' =>  'required|image|mimes:jpeg,png,jpg,svg',
        ]);

        if(!$validator->fails()){

            $imagem = $request->file('imagem');
            $content = Content::find($id);

            if($imagem){
                File::delete(public_path("/content/display/".$content->imagem));
                $dest = public_path('content/display');
                $photoName = md5(time().rand(0,9999)).'.jpg';
        
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
            $content = Content::find($id);

            if($imagem){
                File::delete(public_path("/content/display/".$content->imagem_responsive));
                $dest = public_path('content/display');
                $photoName = md5(time().rand(0,9999)).'.jpg';
        
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
