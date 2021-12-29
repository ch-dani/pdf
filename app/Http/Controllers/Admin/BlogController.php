<?php

namespace App\Http\Controllers\Admin;

use App\Blog;
use App\Http\Controllers\Controller;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use View;

class BlogController extends Controller
{
    public function index()
    {
        return view('admin.articles', [
            'Articles' => Blog::orderBy('id', 'desc')->get(),
            'js' => [
                'https://cdn.ckeditor.com/4.5.11/full/ckeditor.js',
                asset('js/admin/blog.js')
            ]
        ]);
    }
    
    public function categories(){
         return view('admin.articles-cats', [
            'Articles' => \App\BlogCategories::orderBy('id', 'desc')->get(),
            'js' => [
                'https://cdn.ckeditor.com/4.5.11/full/ckeditor.js',
                asset('js/admin/blog.js')
            ]
        ]);   
    }

    public function categoriesEdit($id=0)
    {
    
        $Article = \App\BlogCategories::find($id);
        
        
        if(!$Article){
        	$Article = (object)['id'=>'new', "title"=>false, "summary"=>false, "content"=>false, "seo_title"=>false, "seo_keywords"=>false, "seo_description"=>false, 
        		"thumbnail"=>null, "url"=>false, "status"=>"active"];
        }
        

//        if (is_null($Article))
//            return redirect(route('admin-articles'));

        return view('admin.article-cats-edit', [
            'Article' => $Article,
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'js' => [
                'https://cdn.ckeditor.com/4.5.11/full/ckeditor.js',
                asset('js/admin/blog.js')
            ]
        ]);
    }

    
    

    public function add()
    {
        return view('admin.article-add', [
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'categories'=>\App\BlogCategories::where("status", "active")->get(),            
            'js' => [
                'https://cdn.ckeditor.com/4.5.11/full/ckeditor.js',
                asset('js/admin/blog.js')
            ]
        ]);
    }

    public function edit($id)
    {
        $Article = Blog::find($id);

        if (is_null($Article))
            return redirect(route('admin-articles'));



        return view('admin.article-edit', [
            'Article' => $Article,
            'categories'=>\App\BlogCategories::where("status", "active")->get(),
            'blog_categories'=>\App\BlogCategoriesAssign::where("blog_id", $Article->id)->pluck('category_id')->toArray(),
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'js' => [
                'https://cdn.ckeditor.com/4.5.11/full/ckeditor.js',
                asset('js/admin/blog.js')
            ]
        ]);
    }

    public function add_article(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|max:255',
            'title' => 'required|max:255',
            'summary' => 'required',
            'content' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);


        $url = $request->input("url");
        if(!$request->input("url")){
		    foreach($request->input('title') as $tit){
		    	if($tit){
		    		$url = str_slug($tit);
		    		break;
		    	}
		    }
        }

		$image = $request->file('thumbnail');
		$path = false;
		if($image){
			$new_name = rand() . '.' . $image->getClientOriginalExtension();
			$image->move(public_path('images'), $new_name);
			$path = ("/images/").$new_name;
			$full_path = public_path('images')."/".$new_name;
//			$img = \Image::make($full_path);
//			$img->resize(555, null);
//			$img->save($full_path);
    	}






        $Article = new Blog;
        if($path){
        	$Article->thumbnail = $path;        
        }
        $Article->status = $request->input('status');
        $Article->title = json_encode($request->input('title'));
        $Article->summary = json_encode($request->input('summary'));
        $Article->content = json_encode($request->input('content'));
        $Article->seo_title = json_encode($request->input('seo_title'));
        $Article->seo_keywords = json_encode($request->input('seo_keywords'));
        $Article->seo_description = json_encode($request->input('seo_description'));
        $Article->url = str_slug($url);
        $Article->save();


		$blog_categories = ($request->post('categories'));        
        \App\BlogCategoriesAssign::where("blog_id", $Article->id)->delete();
        if($blog_categories){
        	foreach($blog_categories as $bc){
        		\App\BlogCategoriesAssign::create(array(
        			"blog_id"=>(int)$Article->id,
        			"category_id"=>$bc
        		));
        	}
        }



        return response()->json([
            'status' => 'success',
            'article_id' => $Article->id
        ]);
    }




    public function categoriesUpdate(Request $request)
    {

		$path = false;
    	
    	
        $Article = \App\BlogCategories::find($request->input('article_id'));
        
        
        
        
        
        if (is_null($Article)){
//            return response()->json([
//                'status' => 'error',
//                'message' => 'Category not found.'
//            ]);


       	}
         
        if($request->input("article_id")=='new'){
        	$Article =  new \App\BlogCategories();
        }
            

        $validator = Validator::make($request->all(), [
            'status' => 'required|max:255',
            'title' => 'required',
//            'summary' => 'required',
//            'content' => 'required'
        ]);
        
        
        

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);
        }
        $url = $request->input("url");

        if(!$request->input("url")){
		    foreach($request->input('title') as $tit){
		    	if($tit){
		    		$url = str_slug($tit);
		    		break;
		    	}
		    }
        }

        
        
        $Article->status = $request->input('status');
        $Article->title = json_encode($request->input('title'));
//        $Article->summary = json_encode($request->input('summary'));
//        $Article->content = json_encode($request->input('content'));
//        $Article->seo_title = json_encode($request->input('seo_title'));
//        $Article->seo_keywords = json_encode($request->input('seo_keywords'));
//        $Article->seo_description = json_encode($request->input('seo_description'));
        $Article->url = str_slug($url);
        $Article->save();

        return response()->json([
            'status' => 'success',
            'article_id' => $Article->id
        ]);
    }



    public function update(Request $request)
    {




		$image = $request->file('thumbnail');
		$path = false;
		if($image){
			$new_name = rand() . '.' . $image->getClientOriginalExtension();
			
			$image->move(public_path('images'), $new_name);
			$path = ("/images/").$new_name;
			$full_path = public_path('images')."/".$new_name;
//			$img = \Image::make($full_path);
//			$img->resize(555, null);
//			$img->save($full_path);
    	}
    	
    	
        $Article = Blog::find($request->input('article_id'));

        if (is_null($Article))
            return response()->json([
                'status' => 'error',
                'message' => 'Article not found.'
            ]);
            

        $validator = Validator::make($request->all(), [
            'status' => 'required|max:255',
            'title' => 'required|max:255',
            'summary' => 'required',
            'content' => 'required'
        ]);
        
        
        

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);
            
        $url = $request->input("url");
        if(!$request->input("url")){
		    foreach($request->input('title') as $tit){
		    	if($tit){
		    		$url = str_slug($tit);
		    		break;
		    	}
		    }
        }

        
        if($path){
        	$Article->thumbnail = $path;
		}        

        $Article->status = $request->input('status');
        $Article->title = json_encode($request->input('title'));
        $Article->summary = json_encode($request->input('summary'));
        $Article->content = json_encode($request->input('content'));
        $Article->seo_title = json_encode($request->input('seo_title'));
        $Article->seo_keywords = json_encode($request->input('seo_keywords'));
        
        
        $Article->seo_description = json_encode($request->input('seo_description'));
        $Article->url = str_slug($url);
        $Article->save();
        


		$blog_categories = ($request->post('categories'));        
        \App\BlogCategoriesAssign::where("blog_id", $Article->id)->delete();
        if($blog_categories){
        	foreach($blog_categories as $bc){
        		\App\BlogCategoriesAssign::create(array(
        			"blog_id"=>(int)$Article->id,
        			"category_id"=>$bc
        		));
        	}
        }
        

        return response()->json([
            'status' => 'success',
            'article_id' => $Article->id
        ]);
    }

    public function delete(Request $request)
    {
        $Article = Blog::find($request->input('article_id'));

        if (is_null($Article))
            return response()->json([
                'status' => 'error',
                'message' => 'Article not found.'
            ]);

        $Article->delete();

        return response()->json(['status' => 'success']);
    }
}
