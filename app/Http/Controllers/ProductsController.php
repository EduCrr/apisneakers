<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Idioma;
use App\Models\ProdutoIdioma;
use App\Models\CategoriaProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Imagem;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => [ 'index', 'privateIndex', 'findOne', 'create', 'findOnePrivate', 'search', 'privateOrderIndex' ] ] );
        
    }

    public function index(Request $request, $lng){
        
        $array = ['error' => ''];
        $products = Produto::where('visivel', 1)->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->paginate(12);
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
 
        if($products){
            $array['produtos'] = $products;
            foreach($products as $key => $item){
                $products[$key]['category'] = $item->category;
                $products[$key]['category']['lng'] = $item->category->idiomas()->where('idioma_id', $lngId->id)->first();
                $products[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['path'] = url('content/products/capa');
        }else{
            $array['error'] = 'Nenhum produto foi encontrado';
            return $array;
        }
        
        return $array; 
    }

    public function privateIndex(Request $request, $lng){
        $id = $request->input('cat');
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();

        
        if (!$id){
            $newId = CategoriaProduto::select('id')->orderBy('posicao', 'asc')->first();
            $id = $newId->id;
        }
       
        $products = CategoriaProduto::find($id)->products()->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->paginate(3);
        if($products){
            $array['itens'] = $products;
             foreach($products as $key => $item){
                $products[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['link'] = 'produtos';
            $array['name'] = 'Produtos';
            $array['path'] = url('content/products/capa');
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
            $newId = CategoriaProduto::select('id')->orderBy('posicao', 'asc')->first();
            $id = $newId->id;
        }

        $products = CategoriaProduto::find($id)->products()->orderBy('posicao', 'asc')->orderBy('criado', 'desc')->get();
        if($products){
            $array['order'] = $products;
            foreach($products as $key => $item){
                $products[$key]['lng'] = $item->idiomas()->where('idioma_id', $lngId->id)->first();
            }
            $array['link'] = 'produtos';
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
           
    }

    public function findOne($id, $lng){
        $array = ['error' => ''];
        $product = Produto::where('visivel', 1)->find($id);
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();

        if($product){
            $array['product'] = $product;
            $array['product']['lng'] = $product->idiomas()->where('idioma_id', $lngId->id)->first();
            $product['category'] = $product->category;
            $product['category']['lng'] = $product->category->idiomas()->where('idioma_id', $lngId->id)->first();
            $product['imagens'] = $product->imagens;
            $array['pathImagens'] = url('content/products/imagens');
            $array['pathBanner'] = url('content/products/banner');
            $array['pathCapa'] = url('content/products/capa');
            return $array;
        }else{
            $array['error'] = 'Nenhum produto foi encontrado';
            return $array;
        }
    }

    public function findOnePrivate($id, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $product = Produto::find($id);

        if($product){
            $array['product'] = $product;
            $array['product']['lng'] = $product->idiomas()->where('idioma_id', $lngId->id)->first();
            $product['category'] = $product->category;
            $product['category']['lng'] = $product->category->idiomas()->where('idioma_id', $lngId->id)->first();
            $product['imagens'] = $product->imagens;
            $array['pathImagens'] = url('content/products/imagens');
            $array['pathBanner'] = url('content/products/banner/');
            $array['pathCapa'] = url('content/products/capa/');
            return $array;
        }else{
            $array['error'] = 'Nenhum produto foi encontrado';
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
            'banner.*' => 'required|image|mimes:jpeg,png,jpg,svg',
            'capa.*' => 'required|image|mimes:jpeg,png,jpg,svg',
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
            $extensionBanner = $request->file('banner')->extension();
            $extensionCapa = $request->file('capa')->extension();


           
                $photoNameBanner = '';
                //banner
                if($banner){
                    $destBanner = public_path('content/products/banner');
                    $photoNameBanner = md5(time().rand(0,9999)).'.'.$extensionBanner;
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
                    $photoNameCapa = md5(time().rand(0,9999)).'.'.$extensionCapa;
                    $imgCapa = Image::make($capa->getRealPath());
                    $imgCapa->fit(550, 550)->save($destCapa.'/'.$photoNameCapa);
                }else{
                    $array['error'] = 'Adicione uma capa';
                    return $array;
                }

                $newProduct = new Produto();
                $newProductIdioma = new ProdutoIdioma();

                $newProductIdioma->titulo = $title;
                $newProductIdioma->descricao = $description;
                $newProductIdioma->titulo_pagina = $titlePg;
                $newProductIdioma->titulo_compartilhamento = $titleCom;
                $newProductIdioma->descricao_pagina = $desPg;
                $newProductIdioma->descricao_compartilhamento = $desCom;
                $newProductIdioma->idioma_id = 1;
                $newProductIdioma->criado = date('Y-m-d H:i:s');
                $newProductIdioma->save();
                
                $newProduct->banner = $photoNameBanner;
                $newProduct->capa = $photoNameCapa;
                $newProduct->id_categoria = $category;
                $newProduct->criado = date('Y-m-d H:i:s');
                $newProduct->posicao = 0;
                $newProduct->save();

                $newProductIdioma->produto_id = $newProduct->id;
                $newProductIdioma->save();

                        
                if($images){
                    foreach($images as $item){
                        $$extension = $item->extension();
                        $dest = public_path('content/products/imagens');
                        $photoName = md5(time().rand(0,9999)).'.'.$extension;
                
                        $img = Image::make($item->getRealPath());
                        $img->fit(550, 550)->save($dest.'/'.$photoName);

                        $newPostPhoto = new Imagem();
                        $newPostPhoto->produto_id = $newProduct->id;
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

        $product = Produto::find($id);
        $produtoIdioma = ProdutoIdioma::where('produto_id', $id)->get();


        if($id){

            //deletar images banco e pasta
            File::delete(public_path("/content/products/banner/".$product->banner));
            File::delete(public_path("/content/products/capa/".$product->capa));
            $imgDel = Imagem::where('produto_id', $product->id)->get();
            foreach($imgDel as $item){
                File::delete(public_path("/content/products/imagens/".$item["imagem"]));
                $item->delete();
            }

            foreach($produtoIdioma as $key => $item){
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
                $product = Produto::find($id);
                //images
                if($images){
                    foreach($images as $item){
                        $extension = $item->extension();
                        $dest = public_path('content/products/imagens');
                        $photoName = md5(time().rand(0,9999)).'.'.$extension;
                
                        $img = Image::make($item->getRealPath());
                        $img->fit(550, 550)->save($dest.'/'.$photoName);

                        $newPostPhoto = new Imagem();
                        $newPostPhoto->produto_id = $product->id;
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

    public function update(Request $request, $id, $lng){

        $array = ['error' => ''];

        $rules = [
            'título' => 'required|max:255',
            'descrição' => 'required',
            'categoria' => 'required',
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
        $titlePg = $request->input('título_da_página');
        $titleCom = $request->input('título_compartilhamento');
        $desPg = $request->input('descrição_da_página');
        $desCom = $request->input('descrição_compartilhamento');
        $product = Produto::find($id);
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $produtoIdioma = ProdutoIdioma::where('produto_id', $id)->where('idioma_id', $lngId->id)->first();

        if($category){
            $product->id_categoria = $category;
            $product->save();
        }

        if($produtoIdioma){
             
            if($title){
                $produtoIdioma->titulo = $title;
            }
    
            if($titlePg){
                $produtoIdioma->titulo_pagina = $titlePg;
            }
    
            if($titleCom){
                $produtoIdioma->titulo_compartilhamento = $titleCom;
            }
    
            if($desPg){
                $produtoIdioma->descricao_pagina = $desPg;
            }
    
            if($desCom){
                $produtoIdioma->descricao_compartilhamento = $desCom;
            }
    
           if($description){
                if($description === '<p><br></p>'){
                    $array['error'] = 'O campo descrição é obrigatório.';
                    return $array;
    
                }else{
                    $produtoIdioma->descricao = $description;
                }
            }
            $produtoIdioma->save();

        }else{
            $newProductIdioma = new ProdutoIdioma();
            $newProductIdioma->titulo = $title;
            $newProductIdioma->descricao = $description;
            $newProductIdioma->titulo_compartilhamento = $titleCom;
            $newProductIdioma->descricao_compartilhamento = $desCom;
            $newProductIdioma->titulo_pagina = $titlePg;
            $newProductIdioma->descricao_pagina = $desPg;
            $newProductIdioma->produto_id = $id;
            $newProductIdioma->idioma_id = $lngId->id;
            $newProductIdioma->criado = date('Y-m-d H:i:s');
            $newProductIdioma->save();
        }
       
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
            $product = Produto::find($id);
            $extension = $request->file('banner')->extension();


            if($banner){
                File::delete(public_path("/content/products/banner/".$product->banner));
                $dest = public_path('content/products/banner');
                $photoName = md5(time().rand(0,9999)).'.'.$extension;
        
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
            $product = Produto::find($id);
            $extension = $request->file('capa')->extension();


            if($capa){
                File::delete(public_path("/content/products/capa/".$product->capa));
                $dest = public_path('content/products/capa');
                $photoName = md5(time().rand(0,9999)).'.'.$extension;
        
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


    public function search(Request $request, $lng){
        $array = ['error' => ''];
        $lngId = Idioma::select('id')->where('codigo', $lng)->first();
        $q = $request->input('q');
        
        if($q){
            $products = ProdutoIdioma::where('titulo', 'LIKE', '%'.$q.'%')->where('idioma_id', $lngId->id)->get();
            foreach($products as $key => $item){
                $product = Produto::find($item->produto_id);
                $products[$key] = $product;
                $products[$key]['lng'] = $item;
            }
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

        $product = Produto::find($id);

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
                $cat = Produto::find($item['id']);
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
