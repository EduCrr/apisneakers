<?php

namespace App\Http\Controllers;

use App\Models\CategoriaPost;
use App\Models\CategoriaPostIdioma;
use App\Models\Idioma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'index', 'privateIndex', 'create', 'showCategory', 'findOneEdit', 'findOnePrivate', 'findAll', 'order', 'findOne' ] ] );  
    }

    public function index(Request $request, $lng){
       
        $array = ['error' => ''];

        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $categories = CategoriaPost::where('visivel', 1)->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->get();

        if($categories){
            foreach($categories as $key => $item){
               $categories[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['categories'] = $categories;
        }else{
            $array['error'] = 'Nenhuma categoria foi encontrada';
            return $array;
        }
        
        return $array;
    }
    
    public function privateIndex(Request $request, $lng){

        $array = ['error' => ''];
        $categories = CategoriaPost::orderBy('posicao', 'asc')->orderBy('criado', 'desc')->get(); 

        $lngId = Idioma::select('id')->where('codigo', $lng)->first();

        if($categories){
            foreach($categories as $key => $item){
               $categories[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['link'] = 'categorias';
            $array['categories'] = $categories;
        }else{
            $array['error'] = 'Nenhum produto foi encontrado';
            return $array;
        }
        
        return $array; 
    }

    public function findOneEdit($id, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $category = CategoriaPost::find($id);
        
        if($category){
            $array['category'] = $category;
            $array['category']['lng'] = $category->idiomas()->where('idioma_id', $lngId->id)->where('id_categoria', $id)->first();
            
        }else{
            $array['error'] = 'Não foi encontrada nenhuma categoria!';
            return $array;
        }
        return $array;
    }
    
    //categoria unica com seus posts

    public function findOnePrivate($id, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        
        $category = CategoriaPost::find($id)->posts()->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->paginate(12);
       
        if($category){
            $array['itens'] = $category;

             foreach($category as $key => $item){
                $category[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }

            $array['path'] = url('content/banner/');
        }else{
            $array['error'] = 'Não foi encontrada nenhuma categoria!';
            return $array;
        }
        return $array;
    }

    //categoria unica com seus posts index

    public function findOne($id, $lng){
       $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $category = CategoriaPost::find($id)->posts()->where('visivel', 1)->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->paginate(3);

        if($category){
            $array['itens'] = $category;
            foreach($category as $key => $item){
                $category[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['path'] = url('content/banner/');
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
    }

    public function findAll(Request $request, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();

        $categories = CategoriaPost::where('visivel', 1)->get();

        if($categories){

            $array['categories'] = $categories;
            foreach($categories as $key => $item){
                $categories[$key]['categoria_lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
                $categories[$key]['posts'] = $item->posts()->has('idiomas')->with(["idiomas" => function($q) use ($lngId) { $q->where('idioma_id', $lngId->id);}])->get();
            }

        }else{
            $array['error'] = 'Nenhuma categoria foi encontrada';
            return $array;
        }
        
        return $array;
    }

    public function create(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'título' => 'required|max:128',
        ]);

        $name = $request->input('título');

        if(!$validator->fails()){
            $catExists = CategoriaPostIdioma::where('titulo', $name)->count();

            if($catExists === 0){
                $newCat = new CategoriaPost();
                $newCat->criado = date('Y-m-d H:i:s');
                $newCat->posicao = 0;
                $newCat->save();

                $newCatIdioma = new CategoriaPostIdioma();
                $newCatIdioma->titulo = $name;
                $newCatIdioma->id_categoria = $newCat->id;
                $newCatIdioma->idioma_id = 1;
                $newCatIdioma->criado = date('Y-m-d H:i:s');
                $newCatIdioma->save();
                
                $array['success'] = 'Categoria criada com sucesso!';


            }else{
                $array['error'] = 'Essa categoria já existe!';
                return $array;
            }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function delete($id){
        $array = ['error' => ''];

        $categoria = CategoriaPost::find($id);
        $categoriaIdioma = CategoriaPostIdioma::where('id_categoria', $id)->get();


        if($id){
            $categoria->delete();

            foreach($categoriaIdioma as $key => $item){
               $item->delete();
            }

        }

        return $array;  
    }

    public function update(Request $request, $id, $lng){
        $array = ['error' => ''];

        $rules = [
            'título' => 'required|max:128',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        } 

        $lngId = Idioma::select('id')->where('codigo', $lng)->first();

        $name = $request->input('título');
        $category = CategoriaPostIdioma::where('id_categoria', $id)->where('idioma_id', $lngId->id)->first();

        if($category){
            if($name){
                $category->titulo = $name;
                $category->save();
            }
        }else{
            $newCatIdioma = new CategoriaPostIdioma();
            $newCatIdioma->titulo = $name;
            $newCatIdioma->id_categoria = $id;
            $newCatIdioma->idioma_id = $lngId->id;
            $newCatIdioma->criado = date('Y-m-d H:i:s');
            $newCatIdioma->save();
        }
   
        return $array;
    }

    public function showCategory(Request $request, $id){
        $array = ['error' => ''];
        $visivel = $request->input('check');

        $category = CategoriaPost::find($id);

        if($category){
            if($visivel === true){
                $category->visivel = 0;
            }else if($visivel === false){
                $category->visivel = 1;
            }
            $category->save();
        }else{
            $array['error'] = 'Categoria não encontrada!';
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
                $cat = CategoriaPost::find($item['id']);
                $cat->posicao = $item['posicao'];
                $cat->save();
            }
        }else{
            $array['error'] = 'Categoria não encontrado!';
            return $array;
        }
        return $array;
    }
}
