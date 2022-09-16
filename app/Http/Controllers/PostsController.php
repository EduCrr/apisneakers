<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use App\Models\Category;
use App\Models\Imagem;
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

    public function index(Request $request){
        
        $array = ['error' => ''];
        date_default_timezone_set('America/Sao_Paulo');
        $date = date('Y-m-d H:i');

        $posts = Post::where('visivel', 1)->where('publish', '<=', $date)->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->paginate(12);

        if($posts){
            
            foreach($posts as $key => $item){
            // $posts[$key]['imagens'] = $item->imagens;
                $posts[$key]['category'] = $item->category;
            }
            $array['posts'] = $posts;
            $array['path'] = url('content/posts/banner/');
        }else{
            $array['error'] = 'Nenhum post foi encontrado';
            return $array;
        }
        
        return $array; 
    }

    // public function privateIndex(Request $request){
       
    //     $array = ['error' => ''];
    //     $posts = Post::select()->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->paginate(3);

    //     if($posts){
            
    //         foreach($posts as $key => $item){
    //         // $posts[$key]['imagens'] = $item->imagens;
    //             $c[$key]['category'] = $item->category;
    //             // if($item['visivel'] === 1){
    //             //     $posts[$key]['visivel'] = true;
    //             // }else{
    //             //     $posts[$key]['visivel'] = false;
    //             // }
    //         }
    //         $array['itens'] = $posts;
    //         $array['link'] = 'posts';
    //         $array['name'] = 'Posts';
    //         $array['path'] = url('content/posts/banner/');
    //     }else{
    //         $array['error'] = 'Nenhum post foi encontrado';
    //         return $array;
    //     }
        
    //     return $array; 
    // }

    // public function privateOrderIndex(Request $request){
       
    //     $array = ['error' => ''];
    //     $posts = Post::select(['id', 'title', 'posicao'])->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->get();

    //     if($posts){
    //         $array['order'] = $posts;
    //         $array['link'] = 'posts';
    //     }else{
    //         $array['error'] = 'Nenhum post foi encontrado';
    //         return $array;
    //     }
        
    //     return $array; 
    // }


    public function privateIndex(Request $request){
        $id = $request->input('cat');
        $array = ['error' => ''];

        if (!$id){
            $newId = Category::select('id')->orderBy('posicao', 'asc')->first();
            $id = $newId->id;
        }
       
        $posts = Category::find($id)->posts()->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->paginate(3);
        if($posts){
            $array['itens'] = $posts;
            $array['link'] = 'posts';
            $array['name'] = 'Posts';
            $array['path'] = url('content/posts/banner');
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
    }

    public function privateOrderIndex(Request $request, $id){
                    
        $array = ['error' => ''];
        
        if ($id === '0'){
            $newId = Category::select('id')->orderBy('posicao', 'asc')->first();
            $id = $newId->id;
        }

        $posts = Category::find($id)->posts()->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->get();
        if($posts){
            $array['order'] = $posts;
            $array['link'] = 'posts';
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
           
    }

    public function findOne($id){
        $array = ['error' => ''];

        $post = Post::where('visivel', 1)->find($id);
        if($post){
            $post['category'] = $post->category;
            $array['pathBanner'] = url('content/posts/banner/');
            $array['post'] = $post;
            return $array;
        }else{
            $array['error'] = 'Nenhum post foi encontrado';
            return $array;
        }
    }

    public function findOnePrivate($id){
        $array = ['error' => ''];

        $post = Post::find($id);
        if($post){
            $post['category'] = $post->category;
            $array['pathBanner'] = url('content/posts/banner/');
            $array['post'] = $post;
            return $array;
        }else{
            $array['error'] = 'Nenhum post foi encontrado';
            return $array;
        }
    }

    public function create(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'título' => 'required',
            'título_da_página' => 'required',
            'título_compartilhamento' => 'required',
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
           
            $photoNameBanner = '';
            //banner
            if($banner){
                $destBanner = public_path('content/posts/banner');
                $photoNameBanner = md5(time().rand(0,9999)).'.jpg';
                $imgBanner = Image::make($banner->getRealPath());
                $imgBanner->fit(550, 550)->save($destBanner.'/'.$photoNameBanner);
            }else{
                $array['error'] = 'O campo imagem é obrigatório.';
                return $array;
            }

            $newPost = new Post();
            $newPost->title = $title;
            $newPost->titulo_pagina = $titlePg;
            $newPost->titulo_compartilhamento = $titleCom;
            $newPost->descricao_pagina = $desPg;
            $newPost->descricao_compartilhamento = $desCom;
            $newPost->banner = $photoNameBanner;
            $newPost->description = $description;
            $newPost->category_id = $category;
            $newPost->created_at = date('Y-m-d H:i:s');
            $newPost->publish = $day;
            $newPost->posicao = 0;
            $newPost->save();

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    public function delete($id){
        $array = ['error' => ''];

        $post = Post::find($id);

        if($id){

            //deletar images banco e pasta
            File::delete(public_path("/content/banner/".$post->banner));
           
            //deletar post banco
            $post->delete();

        }

        return $array;  
    }
    
    public function update(Request $request, $id){
        $array = ['error' => ''];

        $rules = [
            'título' => 'required',
            'descrição' => 'required',
            'categoria' => 'required',
            'dia' => 'required',
            'título_da_página' => 'required',
            'título_compartilhamento' => 'required',
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

        if($title){
            $post->title = $title;
        }

        if($description){
            if($description === '<p><br></p>'){
                $array['error'] = 'O campo descrição é obrigatório.';
                return $array;

            }else{
                $post->description = $description;
            }
        }

        if($category){
            $post->category_id = $category;
        }

        if($day){
            $post->publish = $day;
        }

         if($titlePg){
            $post->titulo_pagina = $titlePg;
        }

        if($titleCom){
            $post->titulo_compartilhamento = $titleCom;
        }

        if($desPg){
            $post->descricao_pagina = $desPg;
        }

        if($desCom){
            $post->descricao_compartilhamento = $desCom;
        }
        
        $post->save();

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

            if($banner){
                File::delete(public_path("/content/posts/banner/".$post->banner));
                $dest = public_path('content/posts/banner');
                $photoName = md5(time().rand(0,9999)).'.jpg';
        
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

    public function search(Request $request){
        $array = ['error' => ''];

        $q = $request->input('q');
        
        if($q){
            $posts = Post::where('title', 'LIKE', '%'.$q.'%')->get();
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
