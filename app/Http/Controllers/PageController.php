<?php

namespace App\Http\Controllers;

use App\GuideTool;
use App\Language;
use App\Page;
use App\Contact;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function getPageGuides(Request $request)
    {
        $Page = Page::find($request->page_id);
        $ActiveLanguage = Language::find($request->active_language);

        $PageGuides = GuideTool::where('tool', $Page->tool)->where('guides.status', 'show')->join('guides', 'guides.id', '=', 'guides_tools.guide_id')
            ->select('guides.*')->distinct()->orderBy('guides.sort', 'asc')->orderBy('guides.id', 'asc')->get();
        $PageGuidesSite = [];

        foreach ($PageGuides as $key => $PageGuide) {
            $PageGuidesSite[$key] = (object)[
                'title' => $PageGuide->title,
                'subtitle' => $PageGuide->subtitle,
                'content' => $PageGuide->content,
            ];

            $titles = json_decode($PageGuide->title, true);
            $subtitles = json_decode($PageGuide->subtitle, true);
            $contents = json_decode($PageGuide->content, true);

            $PageGuidesSite[$key]->title = (isset($titles[$ActiveLanguage->id]) and !empty($titles[$ActiveLanguage->id])) ? $titles[$ActiveLanguage->id] : (isset($titles[1]) ? $titles[1] : '');
            $PageGuidesSite[$key]->subtitle = (isset($subtitles[$ActiveLanguage->id]) and !empty($subtitles[$ActiveLanguage->id])) ? $subtitles[$ActiveLanguage->id] : (isset($subtitles[1]) ? $subtitles[1] : '');
            $PageGuidesSite[$key]->content = htmlspecialchars_decode((isset($contents[$ActiveLanguage->id]) and !empty($contents[$ActiveLanguage->id])) ? $contents[$ActiveLanguage->id] : (isset($contents[1]) ? $contents[1] : ''));
        }

        $response = $this->formatResponse('success', null, $PageGuidesSite);
        return response($response, 200);
    }

    public function contact()
    {
        return view('contact');
    }

    public function storeContactRequest(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required|min:6',
        ]);

        Contact::create($validatedData);

        return response()->json(['status' => 'success', 'message' => 'Your message was sent']);
    }
}
