<?php

namespace App\Http\Controllers;

use App\Models\CategorieProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesProductsController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'index', 'privateIndex', 'create', 'findOneEdit', 'findAll' ] ] );  
    }
   
    public function privateIndex(Request $request){
        $array = ['error' => ''];
        $categories = CategorieProduct::all();
        $array['categories'] = $categories;
        $array['link'] = 'categorias-produtos';

        
        return $array;
    }

    public function index(Request $request){
       
        $array = ['error' => ''];
        $categories = CategorieProduct::where('visivel', 1)->get();

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
        $category = CategorieProduct::find($id);
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
        $category = CategorieProduct::find($id)->products()->paginate(1);
        if($category){
            foreach($category as $key => $item){
                if($item['visivel'] === 1){
                    $category[$key]['visivel'] = true;
                }else{
                    $category[$key]['visivel'] = false;
                }
            }
            $array['itens'] = $category;
            $array['path'] = url('content/products/banner');
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
    }

    public function findOne($id){
        $array = ['error' => ''];
        $category = CategorieProduct::find($id)->where('visivel', 1)->products()->paginate(1);
        if($category){
            foreach($category as $key => $item){
                if($item['visivel'] === 1){
                    $category[$key]['visivel'] = true;
                }else{
                    $category[$key]['visivel'] = false;
                }
            }
            $array['itens'] = $category;
            $array['path'] = url('content/products/banner');
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
    }

    public function findAll(Request $request){
        $array = ['error' => ''];
        $categories= CategorieProduct::where('visivel', 1)->with('products')->get();

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
            $catExists = CategorieProduct::where('name', $name)->count();

            if($catExists === 0){
                
                $newCat = new CategorieProduct();
                $newCat->name = $name;
                $newCat->save();
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

        $category = CategorieProduct::find($id);

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
        $category = CategorieProduct::find($id);

        if($name){
            $category->name = $name;
        }
   
        $category->save();
        return $array;
    }

    public function showCategory(Request $request, $id){
        $array = ['error' => ''];
        $visivel = $request->input('check');

        $category = CategorieProduct::find($id);

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
}
