<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;

class ArticleController extends Controller
{
    
    public function index(Request $request)
    {
        if(!$request->ajax()) 
            return redirect('/');

        # Variables para las busquedas
        $text_search     = $request->search;
        $search_criteria = $request->criteria;


        if($text_search == '')
            $articles = Article::join('categories','articles.category_id', '=', 'categories.id')
                ->select('articles.id','articles.category_id', 'articles.code','articles.name','articles.sale_price','articles.description', 'articles.stock', 'articles.active', 'categories.name as category_name')
                ->paginate(10);
        else 
            $articles = Article::join('categories','articles.category_id', '=', 'categories.id')
                ->select('articles.id','articles.category_id', 'articles.code','articles.name','articles.sale_price','articles.description', 'articles.stock', 'articles.active', 'categories.name as category_name')
                ->where('articles.'.$search_criteria, 'like', '%'. $text_search . '%')
                ->paginate(10);

        
        # retornamos un array con los metodos necesarios
        # para controlar la paginacion
        return [
            'pagination' => [
                'total'         =>  $articles->total(),
                'current_page'  =>  $articles->currentPage(),
                'per_page'      =>  $articles->perPage(),
                'last_page'     =>  $articles->lastPage(),
                'from'          =>  $articles->firstItem(),
                'to'            =>  $articles->lastItem(),
            ],
            'articles' => $articles

        ];
    }

    public function store(Request $request)
    {
        
        if(!$request->ajax()) 
            return redirect('/');

        $article                = new Article();
        $article->name          = $request->name;
        $article->description   = $request->description;
        $article->code          = $request->code;
        $article->sale_price    = $request->sale_price;
        $article->stock         = $request->stock;
        $article->category_id   = $request->category_id;
        $article->active        = true;

        $article->save(); 
    }

    public function update(Request $request){
        if(!$request->ajax()) 
            return redirect('/');

        $article                = Article::findOrFail($request->id);
        $article->name          = $request->name;
        $article->description   = $request->description;
        $article->code          = $request->code;
        $article->sale_price    = $request->sale_price;
        $article->stock         = $request->stock;
        $article->category_id   = $request->category_id;
     
        $article->save(); 
    }

    public function changeStatus(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        $article = Article::findOrFail($request->id);
       
        if($article->active)
            $article->active = false;
        else
            $article->active = true;
       
        $article->save(); 
    }

    // Busca un solo articulo dado su codigo
    public function searchArticle(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        $filter = $request->filter; // codigo de barras

        $article = Article::where('code','=',$filter)
        ->select('id','name')
        ->orderBy('name','asc')->take(1)->get();

        return ['article' => $article];

    }

    // Busca un solo articulo dado su codigo, solo si esta disponible su venta, e
    // es decir su stock sea > 0,
    public function searchArticleStock(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        $filter = $request->filter; // codigo de barras

        $article = Article::where('code','=',$filter)
        ->select('id','code','name','stock','sale_price')
        ->where('stock','>','0')
        ->orderBy('name','asc')->take(1)->get();

        return ['article' => $article];

    }
    
    // Una version de la funcion index, pero filtrando Todos
    // aquellos articulos disponibles para su venta.
    // Porque cuando se despliega el modal para agregar articulos,
    // se deben mostrar soslo los articulos disponibles
    public function getArticlesToSale(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        # Variables para las busquedas
        $text_search     = $request->search;
        $search_criteria = $request->criteria;


        if($text_search == '')
            $articles = Article::join('categories','articles.category_id', '=', 'categories.id')
                ->select('articles.id','articles.category_id', 'articles.code','articles.name','articles.sale_price','articles.description', 'articles.stock', 'articles.active', 'categories.name as category_name')
                ->where('articles.stock','>','0')
                ->paginate(10);
        else 
            $articles = Article::join('categories','articles.category_id', '=', 'categories.id')
                ->select('articles.id','articles.category_id', 'articles.code','articles.name','articles.sale_price','articles.description', 'articles.stock', 'articles.active', 'categories.name as category_name')
                ->where('articles.'.$search_criteria, 'like', '%'. $text_search . '%')
                ->where('articles.stock','>','0')                
                ->paginate(10);

        
        # retornamos un array con los metodos necesarios
        # para controlar la paginacion
        return [
            'pagination' => [
                'total'         =>  $articles->total(),
                'current_page'  =>  $articles->currentPage(),
                'per_page'      =>  $articles->perPage(),
                'last_page'     =>  $articles->lastPage(),
                'from'          =>  $articles->firstItem(),
                'to'            =>  $articles->lastItem(),
            ],
            'articles' => $articles
        ];
    }

    public function listPDF(){
        $articles = Article::join('categories','articles.category_id', '=', 'categories.id')
                ->select('articles.id','articles.category_id', 'articles.code','articles.name','articles.sale_price','articles.description', 'articles.stock', 'articles.active', 'categories.name as category_name')
                ->orderBy('articles.name','desc')->get();
        
        $count = Article::count();
        $pdf = \PDF::loadView('pdf.articlespdf',['articles' => $articles, 'count'=>$count]);
        // Vista articlespdf en la carpeta pdf, se le envia el array.
        return $pdf->download('articulos.pdf');
    }
    
}

