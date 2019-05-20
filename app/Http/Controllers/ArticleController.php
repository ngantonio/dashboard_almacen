<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        #if(!$request->ajax()) 
        #    return redirect('/');

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    public function searchArticle(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        $filter = $request->filter; // codigo de barras

        $article = Article::where('code','=',$filter)
        ->select('id','name')
        ->orderBy('name','asc')->take(1)->get();

        return ['article' => $article];

    }

    
}

