<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use App\Models\PostIdioma;
use App\Models\CategoriaPost;
use App\Models\Idioma;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use DateTime;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'index', 'privateIndex', 'findOne', 'findOnePrivate', 'search', 'privateOrderIndex' ] ] );  
    }

    public function index(Request $request, $lng){
         date_default_timezone_set('America/Sao_Paulo');
        $date = date('Y-m-d H:i');
        $id = $request->input('cat');
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $array = ['error' => ''];

        if (!$id){
            $newId = CategoriaPost::select('id')->orderBy('posicao', 'asc')->first();
            $id = $newId->id;
        }

        $posts = CategoriaPost::find($id)->posts()->where('visivel', 1)->where('publicado', '<=', $date)->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->paginate(3);
        if($posts){
            $array['itens'] = $posts;
             foreach($posts as $key => $item){
                $posts[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['link'] = 'posts';
            $array['name'] = 'Posts';
            $array['path'] = url('content/posts/banner');
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
    }

   
    //aqui pode ser o mesmo que categoriescontroller
    public function privateIndex(Request $request, $lng){
        $id = $request->input('cat');
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $array = ['error' => ''];

        if (!$id){
            $newId = CategoriaPost::select('id')->orderBy('posicao', 'asc')->first();
            $id = $newId->id;
        }

        $posts = CategoriaPost::find($id)->posts()->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->paginate(3);
        if($posts){
            $array['itens'] = $posts;
             foreach($posts as $key => $item){
                $posts[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['link'] = 'posts';
            $array['name'] = 'Posts';
            $array['path'] = url('content/posts/banner');
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
    }

    public function privateOrderIndex(Request $request, $id, $lng){
                    
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();

        if ($id === '0'){
            $newId = CategoriaPost::select('id')->orderBy('posicao', 'asc')->first();
            $id = $newId->id;
        }

        $posts = CategoriaPost::find($id)->posts()->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->get();
        if($posts){
            $array['order'] = $posts;
            foreach($posts as $key => $item){
                $posts[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['link'] = 'posts';
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
           
    }

    public function findOne($id, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $post = Post::where('visivel', 1)->find($id);

        if($post){
            $array['post'] = $post;
            $array['post']['lng'] = $post->idiomas()->where('idioma_id', $lngId->id)->first();
            $post['category'] = $post->category;
            $post['category']['lng'] = $post->category->idiomas()->where('idioma_id', $lngId->id)->first();
            $array['pathBanner'] = url('content/posts/banner/');
            return $array;
        }else{
            $array['error'] = 'Nenhum post foi encontrado';
            return $array;
        }
    }

    public function findOnePrivate($id, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $post = Post::find($id);

        if($post){
            $array['post'] = $post;
            $array['post']['lng'] = $post->idiomas()->where('idioma_id', $lngId->id)->first();
            $post['category'] = $post->category;
            $post['category']['lng'] = $post->category->idiomas()->where('idioma_id', $lngId->id)->first();
            $array['pathBanner'] = url('content/posts/banner/');
            return $array;
        }else{
            $array['error'] = 'Nenhum post foi encontrado';
            return $array;
        }
    }

    public function create(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'título' => 'required|max:255',
            'título_da_página' => 'required|max:60',
            'título_compartilhamento' => 'required|max:60',
            'descrição_da_página' => 'required',
            'descrição_compartilhamento' => 'required',
            'imagem.*' => 'required|image|mimes:jpeg,png,jpg,svg',
            'descrição' => 'required',
            'categoria' => 'required',
            'dia' => 'required',
        ]);

        if(!$validator->fails()){

            $title = $request->input('título');
            $titlePg = $request->input('título_da_página');
            $titleCom = $request->input('título_compartilhamento');
            $desPg = $request->input('descrição_da_página');
            $desCom = $request->input('descrição_compartilhamento');
            $banner = $request->file('imagem');
            $category = $request->input('categoria');
            $description = $request->input('descrição');
            $day = $request->input('dia');
            $extension = $request->file('imagem')->extension();

            $photoNameBanner = '';
            //banner
            if($banner){
                $destBanner = public_path('content/posts/banner');
                $photoNameBanner = md5(time().rand(0,9999)).'.'.$extension;
                $imgBanner = Image::make($banner->getRealPath());
                $imgBanner->fit(550, 550)->save($destBanner.'/'.$photoNameBanner);
            }else{
                $array['error'] = 'O campo imagem é obrigatório.';
                return $array;
            }

            $newPost = new Post();
            $newPostIdioma = new PostIdioma();
            
            $newPostIdioma->titulo = $title;
            $newPostIdioma->descricao = $description;
            $newPostIdioma->titulo_pagina = $titlePg;
            $newPostIdioma->titulo_compartilhamento = $titleCom;
            $newPostIdioma->descricao_pagina = $desPg;
            $newPostIdioma->descricao_compartilhamento = $desCom;
            $newPostIdioma->idioma_id = 1;
            $newPostIdioma->criado = date('Y-m-d H:i:s');
            $newPostIdioma->save();

            $newPost->banner = $photoNameBanner;
            $newPost->id_categoria = $category;
            $newPost->criado = date('Y-m-d H:i:s');
            $newPost->publicado = $day;
            $newPost->posicao = 0;
            $newPost->save();

            $newPostIdioma->post_id = $newPost->id;
            $newPostIdioma->save();

            

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    public function delete($id){
        $array = ['error' => ''];

        $post = Post::find($id);
        $postIdioma = PostIdioma::where('post_id', $id)->get();

        if($id){

            //deletar images banco e pasta
            File::delete(public_path("/content/banner/".$post->banner));
           
            //deletar post banco
            $post->delete();

            foreach($postIdioma as $key => $item){
               $item->delete();
            }

        }

        return $array;  
    }
    
    public function update(Request $request, $id, $lng){
        $array = ['error' => ''];

        $rules = [
            'título' => 'required|max:255',
            'descrição' => 'required',
            'categoria' => 'required|max:255',
            'dia' => 'required',
            'título_da_página' => 'required|max:60',
            'título_compartilhamento' => 'required|max:60',
            'descrição_da_página' => 'required',
            'descrição_compartilhamento' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        } 

        $title = $request->input('título');
        $description = $request->input('descrição');
        $category = $request->input('categoria');
        $day = $request->input('dia');
        $titlePg = $request->input('título_da_página');
        $titleCom = $request->input('título_compartilhamento');
        $desPg = $request->input('descrição_da_página');
        $desCom = $request->input('descrição_compartilhamento');
        $post = Post::find($id);
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $postIdioma = PostIdioma::where('post_id', $id)->where('idioma_id', $lngId->id)->first();

        if($day){
            $post->publicado = $day;
            $post->save();
        }

        if($category){
            $post->id_categoria = $category;
            $post->save();
        }

        if($postIdioma){
             
            if($title){
                $postIdioma->titulo = $title;
            }

            if($description){
                if($description === '<p><br></p>'){
                    $array['error'] = 'O campo descrição é obrigatório.';
                    return $array;

                }else{
                    $postIdioma->descricao = $description;
                }
            }

            if($titlePg){
                $postIdioma->titulo_pagina = $titlePg;
            }

            if($titleCom){
                $postIdioma->titulo_compartilhamento = $titleCom;
            }

            if($desPg){
                $postIdioma->descricao_pagina = $desPg;
            }

            if($desCom){
                $postIdioma->descricao_compartilhamento = $desCom;
            }
            
            $postIdioma->save();

        }else{
            $newPostIdioma = new PostIdioma();
            $newPostIdioma->titulo = $title;
            $newPostIdioma->descricao = $description;
            $newPostIdioma->titulo_compartilhamento = $titleCom;
            $newPostIdioma->descricao_compartilhamento = $desCom;
            $newPostIdioma->titulo_pagina = $titlePg;
            $newPostIdioma->descricao_pagina = $desPg;
            $newPostIdioma->post_id = $id;
            $newPostIdioma->idioma_id = $lngId->id;
            $newPostIdioma->criado = date('Y-m-d H:i:s');
            $newPostIdioma->save();
        }

        return $array;

    }

    public function banner(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'banner' =>  'image|mimes:jpeg,png,jpg,svg',
        ]);


        if(!$validator->fails()){

            $banner = $request->file('banner');
            $post = Post::find($id);
            $extension = $request->file('banner')->extension();


            if($banner){
                File::delete(public_path("/content/posts/banner/".$post->banner));
                $dest = public_path('content/posts/banner');
                $photoName = md5(time().rand(0,9999)).'.'.$extension;
        
                $img = Image::make($banner->getRealPath());
                $img->fit(550, 550)->save($dest.'/'.$photoName);

                $post->banner = $photoName;
                $post->save();
                
            }


        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    public function postImage(Request $request){
        $request->validate([
            'file' => 'image'
        ]);

        $ext = $request->file->extension();
        $imageName = time().'.'.$ext;

        $request->file->move(public_path('content/files'), $imageName);

        return [
            'location' => asset('content/files/'.$imageName)
        ];
    }

    public function search(Request $request, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $q = $request->input('q');
        
        if($q){
            $posts = PostIdioma::where('titulo', 'LIKE', '%'.$q.'%')->where('idioma_id', $lngId->id)->get();
           
            foreach($posts as $key => $item){
                $post = Post::find($item->post_id);
                $posts[$key] = $post;
                $posts[$key]['lng'] = $item;
            }

            $array['itens']['data'] = $posts;
        }else{
            $array['error'] = 'Digite algo para buscar!';
            return $array;
        }
        $array['path'] = url('content/banner/');
        return $array;

    }

    public function showPost(Request $request, $id){
        $array = ['error' => ''];
        $visivel = $request->input('check');

        $post = Post::find($id);

        if($post){
            if($visivel === true){
                $post->visivel = 0;
            }else if($visivel === false){
                $post->visivel = 1;
            }
            $post->save();
        }else{
            $array['error'] = 'Post não encontrado!';
            return $array;
        }

        return $array;

    }

    public function order(Request $request){
        $array = ['error' => ''];
        $itens = $request->input('itens');
        $data = json_decode($itens, TRUE);
        
        if($data){
            foreach($data as $key => $item){
                $cat = Post::find($item['id']);
                $cat->posicao = $item['posicao'];
                $cat->save();
            }
        }else{
            $array['error'] = 'Post não encontrado!';
            return $array;
        }
        return $array;
    }


}
