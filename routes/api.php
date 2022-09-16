<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ContentsController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaginasController;





/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



//user

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']); 
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::get('/user', [UserController::class, 'read']); 
Route::post('/user', [UserController::class, 'update']); 
Route::post('/user/avatar', [UserController::class, 'updateAvatar']); 
//Route::post('/user', [AuthController::class, 'create']); 
 
//categorie post

Route::post('/categorie/visivel/{id}', [CategoriesController::class, 'showCategory']); 
Route::get('/categories/private', [CategoriesController::class, 'privateIndex']);
Route::get('/categories', [CategoriesController::class, 'index']);
Route::get('/categorie/{id}', [CategoriesController::class, 'findOnePrivate']);
Route::get('/categorie/edit/{id}', [CategoriesController::class, 'findOneEdit']);
Route::post('/categorie', [CategoriesController::class, 'create']);
Route::delete('/categorie/{id}', [CategoriesController::class, 'delete']);
Route::get('/categorie', [CategoriesController::class, 'findAll']);
Route::post('/categorie/{id}', [CategoriesController::class, 'update']);
Route::post('/order/category', [CategoriesController::class, 'order']);


//posts

Route::get('search/posts', [PostsController::class, 'search']); 
Route::post('/private/posts', [PostsController::class, 'privateIndex']);
Route::get('/posts', [PostsController::class, 'index']);
Route::get('/posts/order/{id}', [PostsController::class, 'privateOrderIndex']);
Route::get('/post/{id}', [PostsController::class, 'findOne']);
Route::get('/post/private/{id}', [PostsController::class, 'findOnePrivate']);
Route::delete('/post/{id}', [PostsController::class, 'delete']);
Route::post('/post', [PostsController::class, 'create']);
Route::post('/post/edit/{id}', [PostsController::class, 'update']); 
Route::post('/post/banner/{id}', [PostsController::class, 'banner']); 
Route::post('/postimage', [PostsController::class, 'postImage']);
Route::post('/post/visivel/{id}', [PostsController::class, 'showPost']); 
Route::post('/order/posts', [PostsController::class, 'order']);



//content

Route::get('/content/{controladora}', [ContentsController::class, 'home']);
Route::post('/content/home/{id}', [ContentsController::class, 'update']); 
Route::post('/content/imagem/{id}', [ContentsController::class, 'imagem']); 
Route::post('/content/imagem/responsive/{id}', [ContentsController::class, 'imagemResponsive']); 


//paginas

Route::get('/pagina/{controladora}', [PaginasController::class, 'home']);
Route::post('/pagina/{id}', [PaginasController::class, 'update']); 
Route::post('/pagina/imagem/{id}', [PaginasController::class, 'imagem']); 


//slide

Route::get('/private/slides', [SliderController::class, 'privateIndex']);
Route::get('/slides', [SliderController::class, 'index']);
Route::get('/slide/private/{id}', [SliderController::class, 'findOnePrivate']);
Route::post('/slide/visivel/{id}', [SliderController::class, 'showSlide']); 
Route::post('/slide', [SliderController::class, 'create']);
Route::post('/slide/edit/{id}', [SliderController::class, 'update']); 
Route::post('/slide/imagem/{id}', [SliderController::class, 'updateImagem']); 
Route::delete('/slide/{id}', [SliderController::class, 'delete']);
Route::post('/order/slide', [SliderController::class, 'order']);



//product

Route::get('search/products', [ProductsController::class, 'search']); 
Route::post('/private/products/', [ProductsController::class, 'privateIndex']); //produto chamado pela sua categoria 
Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/order/{id}', [ProductsController::class, 'privateOrderIndex']);
Route::get('/product/{id}', [ProductsController::class, 'findOne']);
Route::get('/product/private/{id}', [ProductsController::class, 'findOnePrivate']);
Route::delete('/product/{id}', [ProductsController::class, 'delete']);
Route::post('/product', [ProductsController::class, 'create']);
Route::post('/product/edit/{id}', [ProductsController::class, 'update']); 
Route::post('/product/banner/{id}', [ProductsController::class, 'banner']); 
Route::post('/product/capa/{id}', [ProductsController::class, 'capa']); 
Route::post('product/images/{id}', [ProductsController::class, 'updateImages']); 
Route::delete('product/imagem/{id}', [ProductsController::class, 'deleteImage']); 
Route::post('/product/visivel/{id}', [ProductsController::class, 'showProduct']);
Route::post('product/images/{id}', [ProductsController::class, 'updateImages']); 
Route::delete('product/imagem/{id}', [ProductsController::class, 'deleteImage']); 
Route::post('/order/products', [ProductsController::class, 'order']);


//categorie products

Route::post('/categorie/product/visivel/{id}', [CategoriesProductsController::class, 'showCategory']); 
Route::get('/categories/product/private', [CategoriesProductsController::class, 'privateIndex']);
Route::get('/categories/product', [CategoriesProductsController::class, 'index']);
Route::get('/categorie/product/{id}', [CategoriesProductsController::class, 'findOnePrivate']);
Route::get('/categorie/product/edit/{id}', [CategoriesProductsController::class, 'findOneEdit']);
Route::post('/create/categorie/product', [CategoriesProductsController::class, 'create']);
Route::delete('/categorie/product/{id}', [CategoriesProductsController::class, 'delete']);
Route::get('/categorie/product', [CategoriesProductsController::class, 'findAll']);
Route::post('/categorie/product/{id}', [CategoriesProductsController::class, 'update']);
Route::post('/order/category/products', [CategoriesProductsController::class, 'order']);