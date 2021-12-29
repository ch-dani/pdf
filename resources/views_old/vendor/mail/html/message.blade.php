@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <a href="{{ route('index') }}">
                <img src="{{ asset('/img/logo-footer.png') }}" style="    width: 150px;"/>
            </a>
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <span style="color: #fff"> &copy; {{ date('Y') }} DeftPDF. All rights reserved.</span>
        @endcomponent
    @endslot
@endcomponent
