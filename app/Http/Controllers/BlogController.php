<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Blog;


class BlogController extends Controller
{
//    public function index()
//    {
//        return view('blog', [
//            'Articles' => Blog::where('status', 'active')->orderBy('id', 'desc')->paginate(5),
//            'User' => Auth::user()
//        ]);
//    }

//    public function article($id, $id2=false)
//    {
//    		
//    
////        $Article = Blog::find($id);

//        $Article = Blog::where("url", $id)->first();
//        if (is_null($Article)){
//          	$Article = Blog::where("url", $id2)->first();
//          	if(is_null($Article)){
//            	return redirect(route('blog'));
//            }
//        }

//        return view('article', [
//            'Article' => $Article,
//            'User' => Auth::user()
//        ]);
//    }


    public function index($page=1){
    	if(!isset($_GET['depl'])){
//	    	exit("maintenance");
    	}
    	
    	
    	
    	$is_ajax = (bool)Request()->input("is_ajax");
    
    
    	$category = Request()->get("category")?Request()->get("category"):'all';
    	$search = Request()->input("search");
    	
		$articles = Blog::where('status', 'active');
		
		
		
		if($category!='all'){
			$articles = $articles->whereHas('categories', function($q) use ($category){
				$q->where("category_id", $category);	
			});
		}
		if($search){
			$articles = $articles->whereRaw('title like ("%'.$search.'%") or content like ("%'.$search.'%") or summary like ("%'.$search.'%")');
		}
		
		$articles = $articles->orderBy('id', 'desc')->paginate(15);
    
    	if($is_ajax){
    		$html = view('blog', [
		    	'blog_categories'=>\App\BlogCategories::where("status", "active")->get(),
		        'Articles' => $articles,
		        'User' => Auth::user()
	        ])->render();
    	
    		return Response()->json(["success"=>true, "html"=>$html, "total_pages"=>$articles->lastPage()]);
    	}
    	
    	
//    	echo "<pre>";
//    	var_dump($articles);
//    	
//    	exit("here?");
//    	
    
        return view('blog', [
        	'blog_categories'=>\App\BlogCategories::where("status", "active")->get(),
            'Articles' => $articles,
            'User' => Auth::user()
        ]);
    }

    public function article($id, $id2=false)
    {
//        $Article = Blog::find($id);
        $Article = Blog::where("url", $id)->first();
        if (is_null($Article)){
          	$Article = Blog::where("url", $id2)->first();
          	if(is_null($Article)){
            	return redirect(route('blog'));
            }
        }

        return view('article', [
        	'is_single_blog'=> true,
        	'other_posts'=>Blog::where("url", "!=", $id)->inRandomOrder()->paginate(3),
            'Article' => $Article,
            'User' => Auth::user()
        ]);
    }



}
