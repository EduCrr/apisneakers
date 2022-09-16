<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'index', 'privateIndex', 'create', 'findOneEdit', 'findOnePrivate', 'findAll', 'order' ] ] );  
    }

    public function privateIndex(Request $request){
        $array = ['error' => ''];
        $categories = Category::orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->get(); 
        $array['categories'] = $categories;
        $array['link'] = 'categorias';
        
        return $array;
    }

    public function index(Request $request){
       
        $array = ['error' => ''];
        $categories = Category::where('visivel', 1)->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->get();

        if($categories){
            $array['categories'] = $categories;
        }else{
            $array['error'] = 'Nenhuma categoria foi encontrada';
            return $array;
        }
        
        return $array;
    } 

    public function findOneEdit($id){
        $array = ['error' => ''];
        $category = Category::find($id);
        if($category){
            $array['category'] = $category;
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
    }
    

    public function findOnePrivate($id){
        $array = ['error' => ''];
        $category = Category::find($id)->posts()->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->paginate(3);
        if($category){
            $array['itens'] = $category;
            $array['path'] = url('content/banner/');
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
    }

    public function findOne($id){
        $array = ['error' => ''];
        $category = Category::find($id)->where('visivel', 1)->posts()->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->paginate(3);
        if($category){
            
            $array['itens'] = $category;
            $array['path'] = url('content/banner/');
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
    }

    public function findAll(Request $request){
        $array = ['error' => ''];
        $categories= Category::where('visivel', 1)->with('posts')->get();

        if($categories){
            $array['categories'] = $categories;
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

        $name = $request->input('título');

        if(!$validator->fails()){
            $catExists = Category::where('name', $name)->count();

            if($catExists === 0){
                
                $newCat = new Category();
                $newCat->name = $name;
                $newCat->created_at = date('Y-m-d H:i:s');
                $newCat->posicao = 0;
                $newCat->save();
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

        $category = Category::find($id);

        if($id){
            $category->delete();
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

        $name = $request->input('título');
        $category = Category::find($id);

        if($name){
            $category->name = $name;
        }
   
        $category->save();
        return $array;
    }

    public function showCategory(Request $request, $id){
        $array = ['error' => ''];
        $visivel = $request->input('check');

        $category = Category::find($id);

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
                $cat = Category::find($item['id']);
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
