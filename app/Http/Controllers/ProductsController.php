<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Imagem;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'index', 'privateIndex', 'findOne', 'findOnePrivate', 'search' ] ] );
        
    }

    public function index(Request $request){
        
        $array = ['error' => ''];

        $products = Product::where('visivel', 1)->orderBy('id', 'desc')->paginate(12);

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
       
        $array = ['error' => ''];
         $products = Product::select()->orderBy('posicao', 'desc')->paginate(12);

        if($products){
            
            foreach($products as $key => $item){
                //$products[$key]['imagens'] = $item->imagens;
                $products[$key]['category'] = $item->category;
                if($item['visivel'] === 1){
                    $products[$key]['visivel'] = true;
                }else{
                    $products[$key]['visivel'] = false;
                }
            }
            $array['itens'] = $products;
            $array['link'] = 'produtos';
            $array['name'] = 'Produtos';
            $array['path'] = url('content/products/capa');
        }else{
            $array['error'] = 'Nenhum Produto foi encontrado';
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
            'banner.*' => 'required|image|mimes:jpeg,png,jpg,svg',
            'categoria' => 'required',
            'descrição' => 'required',
            'imagens.*' =>  'required|image|mimes:jpeg,png,jpg,svg',
        ]);

        if(!$validator->fails()){

            $title = $request->input('título');
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
                $newProduct->banner = $photoNameBanner;
                $newProduct->capa = $photoNameCapa;
                $newProduct->description = $description;
                $newProduct->categorie_product_id = $category;
           
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
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        } 

        $title = $request->input('título');
        $description = $request->input('descrição');
        $category = $request->input('categoria');
        $product = Product::find($id);

        if($title){
            $product->title = $title;
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

            foreach($products as $key => $item){
                if($item['visivel'] === 1){
                    $products[$key]['visivel'] = true;
                }else{
                    $products[$key]['visivel'] = false;
                }
            }

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
