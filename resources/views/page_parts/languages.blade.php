 @php
global $lang_code;
$url = Request::getRequestUri();


$url = explode("/", $url);
$url = end($url);

if($url == $lang_code){
    $url = "/";
}
//                    	$url = str_replace("/{$lang_code}", "", $url);

if(isset($is_single_blog) and $is_single_blog ){
    $url = "blog/{$url}";
}


if(!$url){
    $url = "/";
}
@endphp
<div class="footer_bottom_item language">
    <div class="switch-language">
        <div class="language-active">
            <a class="language-link SelectLanguage">
 	       	<img src="{{ $ActiveLanguage->flag }}"/>{{ $ActiveLanguage->name }}
            </a>
        </div>

       

        <ul class="languagepicker">

            @foreach ($SiteLanguages as $language)

                @if ($language->code=='en')
                    @php
                        $lang_url = "/".ltrim($url, "/");
                    @endphp
                    <li><a class="language-link SelectLanguage" href="{{ $lang_url }}" data-id="{{ $language->id }}"><img src="{{ $language->flag }}"/>{{ $language->name }}</a></li>
                @else
                    @php
                        $lang_url = "/{$language->code}/{$url}";
                        $lang_url = "/".ltrim(str_replace("//", "/", $lang_url), "/");
                    @endphp
                    <li><a class="language-link SelectLanguage" href="{{ $lang_url }}" data-id="{{ $language->id }}"><img src="{{ $language->flag }}"/>{{ $language->name }}</a></li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
