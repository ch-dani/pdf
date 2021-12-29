<?php

namespace App\Http\Controllers;

use App\Faq;
use App\Page;
use App\User;
use App\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use File;

class HomeController extends Controller
{
    public function deleteInActiveUsers()
    {
        $time = date("Y-m-d H:i:s", time() - 172800);

        $users = \App\User::where([
            ["status", "=", "register"],
            ["created_at", "<", $time]
        ])->get();
        if ($users) {
            var_dump($users->toArray());

            foreach ($users as $us) {
                $us->delete();
            }
            return response()->json(["success" => true, "message" => "cleaned"]);
        } else {
            return response()->json(["success" => true, "message" => "no users"]);
        }
    }

//	public function get_page_langed($lang, $link){
//		return $this->get_page($link);
//	}

    public function get_page($lang = 'en', $link = '/')
    {

    
        $l = $lang;
        $lang = \App\Language::where("code", \Request::segment(1))->first();
        if (!$lang) {
            $lang = \App\Language::find(1);
            $link = $l;
        }

        $langId = $lang->id;

//    	if(!$link || $link == '/' && !$lang_obj){
//    		$link = $lang;
//    		$lang = 'en';
//    	}

//
//    	if($lang and !\Request::segment(2)){
//    		return $this->index();
//    	}

        $Page = Page::where('link', $link)->first();
        if (is_null($Page)) {
            abort(404);
        } else {
            $page = $Page->default_link;
        }

        if ($page == 'index') {
            return $this->index();
        } elseif ($page == 'pricing') {
            return $this->pricing();
        }

        if (view()->exists($page)) {
            if (File::exists(public_path() . '/js/tools/' . $Page->default_link . '.js'))
                $js = [
                    asset('/js/tools/' . $Page->default_link . '.js')
                ];
            else
                $js = [];


            return view($page, [
                'js' => $js,
                'page' => $Page,
                'testimonials' => Page::where('default_link', 'index')->first()->blocks,
                'lang_id' => $langId,
                "pricing" => json_encode([
                    "translate_count" => (float)\App\Option::option('translate_count'),
                    "translate_pricing" => (float)\App\Option::option('translate_price'),
                    "free_count" => (float)\App\Option::option('free_translate_count'),
                ])

                //(\App\TranslatePricing::orderBy('id', 'asc')->get()->toJson())
            ]);
        } else {
            return view('template', [
                'page' => $Page
            ]);
        }
    }

    public function index()
    {
        $lang = \App\Language::where("code", \Request::segment(1))->first();
        if (!$lang) {
            $lang = \App\Language::find(1);
        }

        $langId = $lang->id;

        return view('index', [
            'Faq' => Faq::where('status', 'show')->orderBy('sort', 'asc')->orderBy('id', 'asc')->get(),
            'testimonials' => Page::where('default_link', 'index')->first()->blocks,
            'lang_id' => $langId,
             'subscriptionPlans' => SubscriptionPlan::all()
        ]);
    }

    public function developers()
    {
        return view('developers', [

        ]);
    }

    public function teachers()
    {
        return view('teachers', [

        ]);
    }

    public function pricing()
    {
        $subscriptionPlans = SubscriptionPlan::all();
        return view('pricing', compact('subscriptionPlans'));
    }

    public function home_redirect()
    {
        if (Auth::check())
            return Auth::user()->role == 'admin' ? redirect(route('admin-dashboard')) : redirect(route('account'));
        else
            return redirect(route('index'));
    }

    public function activation($userId, $token)
    {
        $user = User::findOrFail($userId);

        if ($user->status == 'register') {
            if ($user->register_token == $token) {
                $user->status = 'active';
                $user->save();

                Auth::login($user, true);
                return redirect(route('account'));
            } else {
                \Session::flash('flash_message_error', 'Not a valid token.');
            }
        } else {
            \Session::flash('flash_message_error', 'Account already confirmed.');
        }
        return redirect('/');
    }

    public function getCountriesList()
    {
        $client = new \GuzzleHttp\Client();
        $result = $client->get('https://restcountries.eu/rest/v2/all');

        if ($result->getStatusCode() !== 200) return response()->json(['status' => 'error', 'message' => 'API call failed']);

        return response()->json(['status' => 'success', 'data' => $result->getBody()->getContents()]);
    }

    public function getSubscriptionPlans()
    {
        return response()->json(['status' => 'success', 'data' => SubscriptionPlan::all()]);
    }

    public function doShowAds()
    {
        $user = Auth::user();
        dump($user);
    }
}
