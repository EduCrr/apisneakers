<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idioma;

class IdiomasController extends Controller
{
    public function index(Request $request){
        
        $array = ['error' => ''];
        $idiomas = Idioma::all();

        if($idiomas){

            $array[] = $idiomas;
            $array['path'] = url('content/flags');
;
           
        }else{
            $array['error'] = 'Nenhum idioma foi encontrado';
            return $array;
        }
        
        return $array; 
    }
}
