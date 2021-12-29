@php
  $seo_ads = json_decode(\App\Option::option('seo_ads'), true);
@endphp
@isset($seo_ads[$ActiveLanguage->id])
    {!! $seo_ads[$ActiveLanguage->id] !!}
@endisset