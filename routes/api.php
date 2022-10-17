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
use App\Http\Controllers\IdiomasController;
use App\Http\Controllers\EmailsController;





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
Route::get('/categorie/{lng}', [CategoriesController::class, 'findAll']);
Route::get('/categories/private/{lng}', [CategoriesController::class, 'privateIndex']);
Route::get('/categories/{lng}', [CategoriesController::class, 'index']);
Route::get('/categorie/{id}/{lng}', [CategoriesController::class, 'findOnePrivate']);
Route::get('/categorie/edit/{id}/{lng}', [CategoriesController::class, 'findOneEdit']);
Route::post('/categorie', [CategoriesController::class, 'create']);
Route::get('/categorie/index/{id}/{lng}', [CategoriesController::class, 'findOne']);
Route::delete('/categorie/{id}', [CategoriesController::class, 'delete']);
Route::post('/categorie/{id}/{lng}', [CategoriesController::class, 'update']);
Route::post('/order/category', [CategoriesController::class, 'order']);


//posts

Route::get('search/posts/{lng}', [PostsController::class, 'search']); 
Route::post('/private/posts/{lng}', [PostsController::class, 'privateIndex']);
Route::post('/posts/{lng}', [PostsController::class, 'index']);
Route::get('/posts/order/{id}/{lng}', [PostsController::class, 'privateOrderIndex']);
Route::get('/post/{id}/{lng}', [PostsController::class, 'findOne']);
Route::get('/post/private/{id}/{lng}', [PostsController::class, 'findOnePrivate']);
Route::delete('/post/{id}', [PostsController::class, 'delete']);
Route::post('/post', [PostsController::class, 'create']);
Route::post('/post/edit/{id}/{lng}', [PostsController::class, 'update']); 
Route::post('/post/banner/{id}', [PostsController::class, 'banner']); 
Route::post('/postimage', [PostsController::class, 'postImage']);
Route::post('/post/visivel/{id}', [PostsController::class, 'showPost']); 
Route::post('/order/posts', [PostsController::class, 'order']);


//content

Route::get('/content/{controladora}/{lng}', [ContentsController::class, 'home']);
Route::get('/content/single/{id}/{lng}', [ContentsController::class, 'contentId']);
Route::post('/content/home/{id}/{lng}', [ContentsController::class, 'update']); 
Route::post('/content/imagem/{id}', [ContentsController::class, 'imagem']); 
Route::post('/content/imagem/responsive/{id}', [ContentsController::class, 'imagemResponsive']); 


//paginas

Route::get('/pagina/{controladora}/{lng}', [PaginasController::class, 'home']);
Route::post('/pagina/imagem/{id}', [PaginasController::class, 'imagem']); 
Route::post('/pagina/{id}/{lng}', [PaginasController::class, 'update']); 


//slide

Route::get('/private/slides/{lng}', [SliderController::class, 'privateIndex']);
Route::get('/slides/{lng}', [SliderController::class, 'index']);
Route::get('/slide/private/{id}/{lng}', [SliderController::class, 'findOnePrivate']);
Route::post('/slide/visivel/{id}', [SliderController::class, 'showSlide']); 
Route::post('/slide', [SliderController::class, 'create']);
Route::post('/slide/edit/{id}/{lng}', [SliderController::class, 'update']); 
Route::post('/slide/imagem/{id}', [SliderController::class, 'updateImagem']); 
Route::post('/slide/mobile/{id}', [SliderController::class, 'updateMobile']); 
Route::delete('/slide/{id}', [SliderController::class, 'delete']);
Route::post('/order/slide', [SliderController::class, 'order']);



//product

Route::get('search/products/{lng}', [ProductsController::class, 'search']); 
Route::post('/private/products/{lng}', [ProductsController::class, 'privateIndex']); //produto chamado pela sua categoria 
Route::get('/products/{lng}', [ProductsController::class, 'index']);
Route::get('/products/order/{id}/{lng}', [ProductsController::class, 'privateOrderIndex']);
Route::get('/product/{id}/{lng}', [ProductsController::class, 'findOne']);
Route::get('/product/private/{id}/{lng}', [ProductsController::class, 'findOnePrivate']);
Route::delete('/product/{id}', [ProductsController::class, 'delete']);
Route::post('/product', [ProductsController::class, 'create']);
Route::post('/product/edit/{id}/{lng}', [ProductsController::class, 'update']); 
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
Route::get('/product/categorie/{lng}', [CategoriesProductsController::class, 'findAll']);
Route::get('/categorie/product/{id}/{lng}', [CategoriesProductsController::class, 'findOnePrivate']);
Route::get('/categorie/product/edit/{id}/{lng}', [CategoriesProductsController::class, 'findOneEdit']);
Route::post('/categorie/product/{id}/{lng}', [CategoriesProductsController::class, 'update']);
Route::get('/categorie/product/index/{id}/{lng}', [CategoriesProductsController::class, 'findOne']);
Route::get('/categories/product/private/{lng}', [CategoriesProductsController::class, 'privateIndex']);
Route::get('/categories/product/{lng}', [CategoriesProductsController::class, 'index']);
Route::post('/create/categorie/product/', [CategoriesProductsController::class, 'create']);
Route::delete('/categorie/product/{id}', [CategoriesProductsController::class, 'delete']);
Route::post('/order/category/products', [CategoriesProductsController::class, 'order']);

//teste
Route::get('/teste/{lng}', [TestesController::class, 'index']);

//Idiomas
Route::get('/idiomas', [IdiomasController::class, 'index']);

// Email
Route::post('/email', [EmailsController::class, 'email']);
