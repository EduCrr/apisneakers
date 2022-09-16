<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class SliderController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'index', 'privateIndex', 'findOnePrivate' ] ] );  
    }

    public function index(Request $request){
       
        $array = ['error' => ''];
        $slides = Slide::where('visivel', 1)->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->get(); 

        if($slides){
            $array['slides'] = $slides;
            $array['path'] = url('content/slides/');
        }else{
            $array['error'] = 'Nenhum imagem foi encontrada';
            return $array;
        }
        
        return $array; 
    }

    public function privateIndex(Request $request){
       
        $array = ['error' => ''];
        $slides = Slide::orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->get();

        if($slides){
            $array['slides'] = $slides;
            $array['link'] = 'slides';
            $array['path'] = url('content/slides/');
        }else{
            $array['error'] = 'Nenhum imagem foi encontrada';
            return $array;
        }
        
        return $array; 
    }

    public function showSlide(Request $request, $id){
        $array = ['error' => ''];
        $visivel = $request->input('check');

        $slide = Slide::find($id);

        if($slide){
            if($visivel === true){
                $slide->visivel = 0;
            }else if($visivel === false){
                $slide->visivel = 1;
            }
            $slide->save();
        }else{
            $array['error'] = 'Imagem não encontrada!';
            return $array;
        }

        return $array;

    }

    public function create(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'título' => 'required',
            'imagem' => 'required|image|mimes:jpeg,png,jpg,svg',
        ]);

        if(!$validator->fails()){

            $title = $request->input('título');
            $imagem = $request->file('imagem');

                $photoNameImagem = '';
                
                if($imagem){
                    $destImg = public_path('content/slides');
                    $photoNameImagem = md5(time().rand(0,9999)).'.jpg';
                    $imgSlide = Image::make($imagem->getRealPath());
                    $imgSlide->fit(1920, 800)->save($destImg.'/'.$photoNameImagem);
                }else{
                    $array['error'] = 'Adicione uma imagem!';
                    return $array;
                }
                
                $newSlide = new Slide();
                $newSlide->title = $title;
                $newSlide->imagem = $photoNameImagem;
                $newSlide->created_at = date('Y-m-d H:i:s');
                $newSlide->posicao = 0;
                $newSlide->save();

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function delete($id){
        $array = ['error' => ''];

        $slide = Slide::find($id);

        if($id){
            File::delete(public_path("/content/slides/".$slide->imagem));
            $slide->delete();
        }

        return $array;  
    }
    public function update(Request $request, $id){
        $array = ['error' => ''];

        $rules = [
            'título' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        } 

        $title = $request->input('título');
        $slide = Slide::find($id);

        if($title){
            $slide->title = $title;
        }
   
        $slide->save();
        return $array;
    }

    public function updateImagem(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'imagem' =>  'required|image|mimes:jpeg,png,jpg,svg',
        ]);


        if(!$validator->fails()){

            $imagem = $request->file('imagem');
            $slide = Slide::find($id);

            if($imagem){
                File::delete(public_path("/content/slides/".$slide->imagem));
                $dest = public_path('content/slides');
                $photoName = md5(time().rand(0,9999)).'.jpg';
        
                $img = Image::make($imagem->getRealPath());
                $img->save($dest.'/'.$photoName);

                $slide->imagem = $photoName;
                $slide->save();
            }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    public function findOnePrivate($id){
        $array = ['error' => ''];

        $slide = Slide::find($id);
        if($slide){
            $array['path'] = url('content/slides/');
            $array['slide'] = $slide;
            return $array;
        }else{
            $array['error'] = 'Nenhuma imagem foi encontrada';
            return $array;
        }
    }

    public function order(Request $request){
        $array = ['error' => ''];
        $itens = $request->input('itens');
        $data = json_decode($itens, TRUE);
        
        if($data){
            foreach($data as $key => $item){
                $cat = Slide::find($item['id']);
                $cat->posicao = $item['posicao'];
                $cat->save();
            }
        }else{
            $array['error'] = 'Imagem não encontrado!';
            return $array;
        }
        return $array;
    }

}
