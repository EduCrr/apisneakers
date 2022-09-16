<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TesteIdioma;
use App\Models\Teste;


class TestesController extends Controller
{
    public function index(Request $request){
        
        $array = ['error' => ''];
        $teste = Teste::all();

        if($teste){

            foreach($teste as $key => $item){
              $teste[$key]['idiomas'] = $item->idiomas;
                          
            }

            $array['info'] = $teste;
           
        }else{
            $array['error'] = 'Nenhum produto foi encontrado';
            return $array;
        }
        
        return $array; 
    }
}
