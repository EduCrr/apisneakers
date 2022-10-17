<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use App\Models\SlideIdioma;
use App\Models\Idioma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class SliderController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'index', 'privateIndex', 'findOnePrivate', 'update' ] ] );  
    }

    public function index(Request $request, $lng){
       
        $array = ['error' => ''];
        $slides = Slide::where('visivel', 1)->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->get(); 
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();

        if($slides){
            
            foreach($slides as $key => $item){
               $slides[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }

            $array['slides'] = $slides;
            $array['path'] = url('content/slides/');
        }else{
            $array['error'] = 'Nenhum imagem foi encontrada';
            return $array;
        }
        
        return $array; 
    }

    public function privateIndex(Request $request, $lng){
       
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $slides = Slide::orderBy('posicao', 'asc')->orderBy('criado', 'desc')->get();

        if($slides){
            foreach($slides as $key => $item){
               $slides[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            
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
            'título' => 'required|max:255',
            'imagem' => 'required|image|mimes:jpeg,png,jpg,svg',
            'mobile' => 'required|image|mimes:jpeg,png,jpg,svg',
        ]);

        if(!$validator->fails()){
            
            $title = $request->input('título');
            $imagem = $request->file('imagem');
            $mobile = $request->file('mobile');
            $extensionImg = $request->file('imagem')->extension();
            $extensionMobile = $request->file('mobile')->extension();


            $photoNameImagem = '';
            $photoNameMobile = '';
            
            if($imagem){
                $destImg = public_path('content/slides');
                $photoNameImagem = md5(time().rand(0,9999)).'.'.$extensionImg;
                $imgSlide = Image::make($imagem->getRealPath());
                $imgSlide->fit(432, 432)->save($destImg.'/'.$photoNameImagem);
            }else{
                $array['error'] = 'Adicione uma imagem!';
                return $array;
            }

            if($mobile){
                $destMobile = public_path('content/slides');
                $photoNameMobile = md5(time().rand(0,9999)).'.'.$extensionMobile;
                $imgMobile = Image::make($mobile->getRealPath());
                $imgMobile->fit(768, 1080)->save($destMobile.'/'.$photoNameMobile);
            }else{
                $array['error'] = 'Adicione uma imagem!';
                return $array;
            }
            
            $newSlide = new Slide();
            $newSlideIdioma = new SlideIdioma();

            $newSlide->imagem = $photoNameImagem;
            $newSlide->imagem_responsive = $photoNameMobile;
            $newSlide->criado = date('Y-m-d H:i:s');
            $newSlide->posicao = 0;
            $newSlide->save();

            
            $newSlideIdioma->titulo = $title;
            $newSlideIdioma->idioma_id = 1;
            $newSlideIdioma->criado = date('Y-m-d H:i:s');
            $newSlideIdioma->save();

            $newSlideIdioma->slide_id = $newSlide->id;
            $newSlideIdioma->save();


        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function delete($id){
        $array = ['error' => ''];


        $slide = Slide::find($id);
        $slideIdiomas = SlideIdioma::where('slide_id', $id)->get();

        if($id){
            File::delete(public_path("/content/slides/".$slide->imagem));
            File::delete(public_path("/content/slides/".$slide->imagem_responsive));
            $slide->delete();

            foreach($slideIdiomas as $key => $item){
               $item->delete();
            }
            
        }

        return $array;  
    }

    public function update(Request $request, $id, $lng){
        $array = ['error' => ''];

        $rules = [
            'título' => 'required|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $title = $request->input('título');
        $slide = SlideIdioma::where('slide_id', $id)->where('idioma_id', $lngId->id)->first();

        if($slide){
            if($title){
               $slide->titulo = $title;
               $slide->save();
            }
        }
        else{
            $newSlideIdioma = new SlideIdioma();
            $newSlideIdioma->titulo = $title;
            $newSlideIdioma->slide_id = $id;
            $newSlideIdioma->idioma_id = $lngId->id;
            $newSlideIdioma->criado = date('Y-m-d H:i:s');
            $newSlideIdioma->save();
        }
        
        return $array;
    }

    public function updateImagem(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'imagem' =>  'image|mimes:jpeg,png,jpg,svg',
        ]);


        if(!$validator->fails()){

            $imagem = $request->file('imagem');
            $slide = Slide::find($id);
           

            if($imagem){
                 $extension = $request->file('imagem')->extension();
                File::delete(public_path("/content/slides/".$slide->imagem));
                $dest = public_path('content/slides');
                $photoName = md5(time().rand(0,9999)).'.'.$extension;
        
                $img = Image::make($imagem->getRealPath());
                $img->fit(432, 432)->save($dest.'/'.$photoName);

                $slide->imagem = $photoName;
                $slide->save();
            }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    public function updateMobile(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'mobile' =>  'image|mimes:jpeg,png,jpg,svg',
        ]);


        if(!$validator->fails()){

            $imagem = $request->file('mobile');
            $slide = Slide::find($id);

            if($imagem){
                $extension = $request->file('mobile')->extension();
                File::delete(public_path("/content/slides/".$slide->imagem_responsive));
                $dest = public_path('content/slides');
                $photoName = md5(time().rand(0,9999)).'.'.$extension;
        
                $img = Image::make($imagem->getRealPath());
                $img->fit(768, 1080)->save($dest.'/'.$photoName);

                $slide->imagem_responsive = $photoName;
                $slide->save();
            }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    public function findOnePrivate($id, $lng){
        $array = ['error' => ''];
        
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $slide = Slide::find($id);
        
        if($slide){
            $array['path'] = url('content/slides/');
            $array['slide'] = $slide;
            $array['slide']['lng'] = $slide->idiomas()->where('idioma_id', $lngId->id)->first();
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
