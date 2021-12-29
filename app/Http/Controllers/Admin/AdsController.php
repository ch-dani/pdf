<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Ads;
use App\Page;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class AdsController extends Controller
{
    public function index(){
    	$pages = Page::get();
    	$prepared_pages = [];
    	
    	foreach($pages as $page){
    		$page_ads = $page->ads->toArray();
    	
    		$prepared_pages[] = (Object)[
    			"id"=>$page->id,
    			"title"=>$page->title,
    			"link"=>$page->link,
    			"ads"=>$page_ads,
    			"ads_count"=>count($page_ads)
    		];
    	}
    	
    	
    	
    	
        return view('admin.ads', [
            'pages' => $prepared_pages,
            'js' => [
                asset('js/admin/ads.js')
            ]
        ]);
    }

    public function edit($id){
    
		$page = Page::find($id);

		if(is_null($page)){
			return redirect(route('ads'));
		}



        return view('admin.ads-edit', [
            'page' => $page,
            'ads' => $page->ads,
            'js' => [
                asset('js/admin/ads.js')
            ]
        ]);
    }

    public function update(Request $request, $id=false){


		$page = Page::find($id);
		$ads = $request->input("ads");
		
		

		if (is_null($page)){
			return response()->json([
				'success' => false,
				'message' => 'Page not found.'
			]);
		}
		
		foreach($page->ads as $ad){
			$ad->delete();
		}
		
		
		if($ads){
			foreach($ads as $it=>$ad){
				$nad = new Ads([
					"content"=>$ad,
					"page_position"=>1,
					"status"=>1
				]);
				$page->ads()->save($nad);	
			}
		}

        return response()->json(['success' => true]);
    }

    public function delete(Request $request)
    {
    	exit("delete");
        $User = User::find($request->input('user_id'));

        if (is_null($User) or $User->role == 'admin')
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ]);

        $User->delete();

        return response()->json(['status' => 'success']);
    }

}
