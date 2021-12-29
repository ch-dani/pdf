@extends('layouts.layout2')

@php
    $accept = "application/pdf";
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded">
        @include('page_parts.toolheader')

        <section class="tool_section section_top hidden after_upload" id="rotate_section">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">
                        {!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : t('Rotate PDF') !!}
                    </h2>
                    <h3 class="sub-title">
                        {!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : t('Rotate and save PDF pages permanently') !!}
                    </h3>
                </div>
                <div class="tools_menu">
                    <div class='block_title'>
                        {{ t('Rotate all pages') }}
                    </div>
                    <div class="buttons_block_row">
                        <div class="buttons_block_1" data-type='pages_rotate'>
                            <button data-val='-90'>
                                <i class="fa fa-undo"></i> <i class="fa fa-rotate-270 fa-font"></i>
                            </button>
                            <button data-val='0' class='active'> 0° <i class="fa fa-font"></i></button>
                            <button data-val='90'>
                                <i class="fa fa-repeat"></i> <i class="fa fa-font fa-rotate-90"></i>
                            </button>
                            <button data-val='180'>
                                <i class="fa fa-refresh"></i> <i class="fa fa-font fa-rotate-180"></i>
                            </button>
                        </div>
                        <div class="buttons_block_1" data-type='pages_selector'>
                            <button data-val='all' class='active'>{{ t("All pages") }}</button>
                            <button data-val='odd'>{{ t("Odd") }}</button>
                            <button data-val='even'>{{ t("Even") }}</button>
                        </div>
                    </div>
                </div>
                <div class='block_title' style="margin-bottom: 30px;">
                    {{ t('Rotate specific pages') }}
                </div>
                <div class="pages_list">
                    <ul id="pages_here">
                    </ul>
                </div>

                @include('page_parts.download_buttons')
            </div>
        </section>

        <section class="module__how-convert module bg-white pb_5">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">{{ t('How to Rotate PDF Files Online Free') }}</h2>
                </div>
                <div class="row">
					@if (count($PageGuides))
						@foreach ($PageGuides as $Guide)
							@if (!is_null($Guide->content))
								{!! htmlspecialchars_decode($Guide->content) !!}
							@endif
						@endforeach
					@endif

					@if(!Auth::id())
						<div class="contact-us">
							<a class="contact-us__button sign-up-trigger" href="{{route("login")}}">{{ t("Sign Up") }}</a>
						</div>
					@endif
                </div>
            </div>
        </section>

        @include('inc-freeconvert.tools-pd')
        @include('inc-freeconvert.banner')
    </main>
@endsection

@section('js')
@endsection
