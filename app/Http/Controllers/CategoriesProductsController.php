<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProduto;
use App\Models\CategoriaProdutoIdioma;
use App\Models\Idioma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesProductsController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'index', 'privateIndex', 'findOne', 'create', 'findOneEdit', 'findOnePrivate', 'findAll', 'order', 'create', 'update', 'delete' ] ] );  
    }
   
    
    public function index(Request $request, $lng){
       
        $array = ['error' => ''];
        $categories = CategoriaProduto::where('visivel', 1)->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->get();  
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();

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

        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $array = ['error' => ''];
        $categories = CategoriaProduto::orderBy('posicao', 'asc')->orderBy('criado', 'desc')->get();  
        if($categories){
            foreach($categories as $key => $item){
               $categories[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }  
            $array['categories'] = $categories;
        }
        $array['link'] = 'categorias-produtos';

        
        return $array;
    }

    public function findOneEdit($id, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $category = CategoriaProduto::find($id);

        if($category){
            $array['category'] = $category;
            $array['category']['lng'] = $category->idiomas()->where('idioma_id', $lngId->id)->where('id_categoria', $id)->first();
        }else{
            $array['error'] = 'Não foi encontrada nenhuma categoria!';
            return $array;
        }
        return $array;
    }
    

    public function findOnePrivate($id, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $category = CategoriaProduto::find($id)->products()->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->paginate(3);

        if($category){
            
            $array['itens'] = $category;

            foreach($category as $key => $item){
                $category[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }

            $array['path'] = url('content/products/banner');
        }else{
            $array['error'] = 'Não foi encontrada nenhuma categoria!';
            return $array;
        }
        return $array;
    }

    public function findOne($id, $lng){
        $array = ['error' => ''];


        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        
        if($id === '0'){
            $getFirstId = CategoriaProduto::select('id')->orderBy('posicao', 'asc')->first();
            $id = $getFirstId->id;
        }

        $category = CategoriaProduto::find($id)->products()->where('visivel', 1)->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->paginate(3);
        
        if($category){
            
            $array['itens'] = $category;

            foreach($category as $key => $item){
                $category[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }

            $array['path'] = url('content/products/capa');
        }else{
            $array['error'] = 'Não foi encontrada nenhuma categoria!';
            return $array;
        }
        return $array;
    }

    public function findAll(Request $request, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();

        $categories = CategoriaProduto::where('visivel', 1)->get();

        if($categories){

            $array['categories'] = $categories;
            foreach($categories as $key => $item){
                $categories[$key]['categoria_lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
                $categories[$key]['products'] = $item->products()->has('idiomas')->with(["idiomas" => function($q) use ($lngId) { $q->where('idioma_id', $lngId->id);}])->get();
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
            'título' => 'required',
        ]);

        $title = $request->input('título');

        if(!$validator->fails()){
            $catExists = CategoriaProdutoIdioma::where('titulo', $title)->count();

            if($catExists === 0){

                $newCat = new CategoriaProduto();
                $newCat->criado = date('Y-m-d H:i:s');
                $newCat->posicao = 0;
                $newCat->save();

                $newCatIdioma = new CategoriaProdutoIdioma();
                $newCatIdioma->titulo = $title;
                $newCatIdioma->id_categoria = $newCat->id;
                $newCatIdioma->idioma_id = 1;
                $newCatIdioma->criado = date('Y-m-d H:i:s');
                $newCatIdioma->save();

                $array['success'] = 'Categoria criada com sucesso!';

            }else{
                $array['error'] = 'Essa Categoria já existe!';
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

        $category = CategoriaProduto::find($id);
        $categoriaIdioma = CategoriaProdutoIdioma::where('id_categoria', $id)->get();


        if($id){
            foreach($categoriaIdioma as $key => $item){
               $item->delete();
            }
            $category->delete();
        }

        return $array;  
    }

    public function update(Request $request, $id, $lng){
        $array = ['error' => ''];

        $rules = [
            'título' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        } 

        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $name = $request->input('título');
        $category = CategoriaProdutoIdioma::where('id_categoria', $id)->where('idioma_id', $lngId->id)->first();
        
        if($category){
            if($name){
                $category->titulo = $name;
                $category->save();
            }
        }else{
            $newCatIdioma = new CategoriaProdutoIdioma();
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

        $category = CategoriaProduto::find($id);

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
                $cat = CategoriaProduto::find($item['id']);
                $cat->posicao = $item['posicao'];
                $cat->save();
            }
           
           
        }else{
            $array['error'] = 'Categoria não encontrada!';
            return $array;
        }

        return $array;

    }
}
