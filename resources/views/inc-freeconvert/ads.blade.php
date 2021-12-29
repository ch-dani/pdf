@php
    $user = Auth::user();
@endphp




@if(!$user || !$user->hasActivePlan)
    @php
       $seo_ads = json_decode(\App\Option::option('seo_ads'), true);
    @endphp
    
    @isset($seo_ads[1])
        <div class="downloader__img">
            {!! str_replace('banner.png"','banner.png" style="height: 250px; object-fit: cover;"',$seo_ads[1]) !!}
        </div>
    @endisset
@endif
