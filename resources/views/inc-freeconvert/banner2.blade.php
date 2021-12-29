@php
    $user = Auth::user();
@endphp
@if(!$user || !$user->hasActivePlan)
    @php
        $seo_ads = json_decode(\App\Option::option('seo_ads'), true);
    @endphp
    @isset($seo_ads[$ActiveLanguage->id])
        <div class="banner">
            {!! $seo_ads[$ActiveLanguage->id] !!}
        </div>
    @endisset
@endif