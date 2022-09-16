<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CategorieProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Imagem;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'index', 'privateIndex', 'findOne', 'findOnePrivate', 'search', 'privateOrderIndex' ] ] );
        
    }

    public function index(Request $request){
        
        $array = ['error' => ''];
        $products = Product::where('visivel', 1)->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->paginate(12);

        if($products){
            
            foreach($products as $key => $item){
             //$products[$key]['imagens'] = $item->imagens;
              $products[$key]['category'] = $item->category;
            }
            $array['posts'] = $products;
            $array['path'] = url('content/products/capa');
        }else{
            $array['error'] = 'Nenhum produto foi encontrado';
            return $array;
        }
        
        return $array; 
    }

    public function privateIndex(Request $request){
        $id = $request->input('cat');
        $array = ['error' => ''];

        if (!$id){
            $newId = CategorieProduct::select('id')->orderBy('posicao', 'asc')->first();
            $id = $newId->id;
        }
       
        $products = CategorieProduct::find($id)->products()->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->paginate(3);
        if($products){
            $array['itens'] = $products;
            $array['link'] = 'produtos';
            $array['name'] = 'Produtos';
            $array['path'] = url('content/products/capa');
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
    }

    public function privateOrderIndex(Request $request, $id){
                    
        $array = ['error' => ''];
        
        if ($id === '0'){
            $newId = CategorieProduct::select('id')->orderBy('posicao', 'asc')->first();
            $id = $newId->id;
        }

        $products = CategorieProduct::find($id)->products()->orderBy('posicao', 'asc')->orderBy('created_at', 'desc')->get();
        if($products){
            $array['order'] = $products;
            $array['link'] = 'produtos';
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
           
    }

    public function findOne($id){
        $array = ['error' => ''];

        $product = Product::where('visivel', 1)->find($id);
        if($product){
            $product['category'] = $product->category;
            $product['imagens'] = $product->imagens;
            $array['pathImagens'] = url('content/products/imagens');
            $array['pathBanner'] = url('content/product/banner');
            $array['pathCapa'] = url('content/product/capa');
            $array['product'] = $product;
            return $array;
        }else{
            $array['error'] = 'Nenhum produto foi encontrado';
            return $array;
        }
    }

    public function findOnePrivate($id){
        $array = ['error' => ''];

        $product = Product::find($id);
        if($product){
            //$product['category'] = $product->category;
            $product['imagens'] = $product->imagens;
            $array['pathImagens'] = url('content/products/imagens');
            $array['pathBanner'] = url('content/products/banner/');
            $array['pathCapa'] = url('content/products/capa/');
            $array['product'] = $product;
            return $array;
        }else{
            $array['error'] = 'Nenhum produto foi encontrado';
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
            'banner.*' => 'required|image|mimes:jpeg,png,jpg,svg',
            'categoria' => 'required',
            'descrição' => 'required',
            'imagens.*' =>  'required|image|mimes:jpeg,png,jpg,svg',
        ]);

        if(!$validator->fails()){

            $title = $request->input('título');
            $titlePg = $request->input('título_da_página');
            $titleCom = $request->input('título_compartilhamento');
            $desPg = $request->input('descrição_da_página');
            $desCom = $request->input('descrição_compartilhamento');
            $banner = $request->file('banner');
            $capa = $request->file('capa');
            $category = $request->input('categoria');
            $description = $request->input('descrição');
          
            $images = $request->file('imagens.*');

           
                $photoNameBanner = '';
                //banner
                if($banner){
                    $destBanner = public_path('content/products/banner');
                    $photoNameBanner = md5(time().rand(0,9999)).'.jpg';
                    $imgBanner = Image::make($banner->getRealPath());
                    $imgBanner->fit(1920, 810)->save($destBanner.'/'.$photoNameBanner);
                }else{
                    $array['error'] = 'Adicione um banner';
                    return $array;
                }

                $photoNameCapa = '';

                //capa
                if($capa){
                    $destCapa = public_path('content/products/capa');
                    $photoNameCapa = md5(time().rand(0,9999)).'.jpg';
                    $imgCapa = Image::make($capa->getRealPath());
                    $imgCapa->fit(550, 550)->save($destCapa.'/'.$photoNameCapa);
                }else{
                    $array['error'] = 'Adicione uma capa';
                    return $array;
                }

                $newProduct = new Product();
                $newProduct->title = $title;
                $newProduct->titulo_pagina = $titlePg;
                $newProduct->titulo_compartilhamento = $titleCom;
                $newProduct->descricao_pagina = $desPg;
                $newProduct->descricao_compartilhamento = $desCom;
                $newProduct->banner = $photoNameBanner;
                $newProduct->capa = $photoNameCapa;
                $newProduct->description = $description;
                $newProduct->categorie_product_id = $category;
                $newProduct->created_at = date('Y-m-d H:i:s');
                $newProduct->posicao = 0;
          
                $newProduct->save();

                if($images){
                    foreach($images as $item){
                        
                        $dest = public_path('content/products/imagens');
                        $photoName = md5(time().rand(0,9999)).'.jpg';
                
                        $img = Image::make($item->getRealPath());
                        $img->fit(550, 550)->save($dest.'/'.$photoName);

                        $newPostPhoto = new Imagem();
                        $newPostPhoto->product_id = $newProduct->id;
                        $newPostPhoto->imagem = $photoName;
                        $newPostPhoto->save();
                    }
                }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    public function delete($id){
        $array = ['error' => ''];

        $product = Product::find($id);

        if($id){

            //deletar images banco e pasta
            File::delete(public_path("/content/products/banner/".$product->banner));
            File::delete(public_path("/content/products/capa/".$product->capa));
            $imgDel = Imagem::where('product_id', $product->id)->get();
            foreach($imgDel as $item){
                File::delete(public_path("/content/products/imagens/".$item["imagem"]));
                $item->delete();
            }

            //deletar post banco
            $product->delete();

        }

        return $array;  
    }

    public function deleteImage($id){
        $array = ['error' => ''];

        $imageDel = Imagem::find($id);

        if($imageDel){
            File::delete(public_path("/content/products/imagens/".$imageDel->imagem));
            $imageDel->delete();

        }else{
            $array['error'] = 'Imagem não existe';
            return $array;
        }

        return $array;

    }

    public function updateImages(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'imagens.*' =>  'required|image|mimes:jpeg,png,jpg,svg',
        ]);

        if(!$validator->fails()){

            $images = $request->file('imagens.*');
                $product = Product::find($id);
                //images
                if($images){
                    foreach($images as $item){
                        
                        $dest = public_path('content/products/imagens');
                        $photoName = md5(time().rand(0,9999)).'.jpg';
                
                        $img = Image::make($item->getRealPath());
                        $img->fit(550, 550)->save($dest.'/'.$photoName);

                        $newPostPhoto = new Imagem();
                        $newPostPhoto->product_id = $product->id;
                        $newPostPhoto->imagem = $photoName;
                        $newPostPhoto->save();
                    }
                }


        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    public function update(Request $request, $id){

        $array = ['error' => ''];

        $rules = [
            'título' => 'required',
            'descrição' => 'required',
            'categoria' => 'required',
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
        $titlePg = $request->input('título_da_página');
        $titleCom = $request->input('título_compartilhamento');
        $desPg = $request->input('descrição_da_página');
        $desCom = $request->input('descrição_compartilhamento');

        $product = Product::find($id);

        if($title){
            $product->title = $title;
        }

        if($titlePg){
            $product->titulo_pagina = $titlePg;
        }

        if($titleCom){
            $product->titulo_compartilhamento = $titleCom;
        }

        if($desPg){
            $product->descricao_pagina = $desPg;
        }

        if($desCom){
            $product->descricao_compartilhamento = $desCom;
        }

       if($description){
            if($description === '<p><br></p>'){
                $array['error'] = 'O campo descrição é obrigatório.';
                return $array;

            }else{
                $product->description = $description;
            }
        }

        if($category){
            $product->categorie_product_id = $category;
        }

        $product->save();
       
        return $array;

    }
    //update banner
    public function banner(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'banner' =>  'image|mimes:jpeg,png,jpg,svg',
        ]);


        if(!$validator->fails()){

            $banner = $request->file('banner');
            $product = Product::find($id);

            if($banner){
                File::delete(public_path("/content/products/banner/".$product->banner));
                $dest = public_path('content/products/banner');
                $photoName = md5(time().rand(0,9999)).'.jpg';
        
                $img = Image::make($banner->getRealPath());
                $img->fit(1920, 810)->save($dest.'/'.$photoName);

                $product->banner = $photoName;
                $product->save();
                
            }


        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    //update capa
    public function capa(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'capa' =>  'image|mimes:jpeg,png,jpg,svg',
        ]);


        if(!$validator->fails()){

            $capa = $request->file('capa');
            $product = Product::find($id);

            if($capa){
                File::delete(public_path("/content/products/capa/".$product->capa));
                $dest = public_path('content/products/capa');
                $photoName = md5(time().rand(0,9999)).'.jpg';
        
                $img = Image::make($capa->getRealPath());
                $img->fit(550, 550)->save($dest.'/'.$photoName);

                $product->capa = $photoName;
                $product->save();
                
            }


        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }


    public function search(Request $request){
        $array = ['error' => ''];

        $q = $request->input('q');
        
        if($q){
            $products = Product::where('title', 'LIKE', '%'.$q.'%')->get();
            $array['itens']['data'] = $products;


        }else{
            $array['error'] = 'Digite algo para buscar!';
            return $array;
        }
        $array['path'] = url('content/products/banner/');
        return $array;

    }

    public function showProduct(Request $request, $id){
        $array = ['error' => ''];
        $visivel = $request->input('check');

        $product = Product::find($id);

        if($product){
            if($visivel === true){
                $product->visivel = 0;
            }else if($visivel === false){
                $product->visivel = 1;
            }
            $product->save();
        }else{
            $array['error'] = 'Produto não encontrado!';
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
                $cat = Product::find($item['id']);
                $cat->posicao = $item['posicao'];
                $cat->save();
            }
        }else{
            $array['error'] = 'Produto não encontrado!';
            return $array;
        }
        return $array;
    }
}
