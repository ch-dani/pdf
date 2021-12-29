@extends('layouts.layout')
@section('content-freeconvert')
    <main class="file_not_loaded">
        <style>
            .crop-edit-top input {
                max-width: 50px;
            }

            .crop-edit-top {
                display: flex;
                padding-bottom: 20px;
                justify-content: space-between;
            }

            .auto_crop {
                background: linear-gradient(90deg, #1CD995 0%, #42E666 100%);
                padding: 10px;
                color: white;
            }
        </style>

        @include('page_parts.toolheader')

        <section class="tool_section crop-section bg-grey after_upload hidden" id="crop_section">
            <div class="container" id="pages_preview_section">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'PDF TOOL' !!}</h2>
                    <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Page subtitle' !!}</h3>
                </div>

                <div class="crop-section__page" id="pages_previews_here">

                </div>
                @include('page_parts.download_buttons')
            </div>
        </section>

        <section class="module__how-convert module bg-white pb_5">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Block #3' !!}</h2>
                </div>
                <div class="row">
                    @if (count($PageGuides))
                        @foreach ($PageGuides as $Guide)
                            @if (!is_null($Guide->content))
                                {!! htmlspecialchars_decode($Guide->content) !!}
                            @endif
                        @endforeach
                    @endif

                </div>
            </div>
        </section>

        @include('inc-freeconvert.banner')

        <?php if(false){ ?>
        <div class="upload-top-info" id="upload_section">
            <div class="container">
                <div class="app-title">
                    <div class="wrapper">
                        <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Crop PDF' !!}</h1>
                        <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Trim PDF margins, change PDF page size' !!}</p>
                    </div>
                </div>

                <div class="welcome_outer">
                    @if($ads && $device_is=='computer')
                        @include("ads.adx250x250")
                    @endif

                    <div class="app-welcome">
                        <form action="#" id="drop_zone">
                            <input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">
                            <div class="upload-img">
                                <img src="/img/pdf-img.svg" alt="">
                            </div>
                            {!! t('<h3>UPLOAD <strong>PDF</strong> FILE</h3>') !!}
                            @include('inc.uploadButton')
                        </form>
                        <div class="upload-welcom-descr">
                            {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                        </div>
                    </div>
                    @if($ads && $device_is=='computer')
                        @include("ads.adx250x250")
                    @endif
                </div>
            </div>

            @if($ads && $device_is=='computer')
                @include("ads.adx970x90")
            @endif

            @if($ads && $device_is=='phone')
                @include("ads.adx320x100")
            @endif

        </div>
        <section id="crop_section" class="hidden">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Crop PDF' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Trim PDF margins, change PDF page size' !!}</p>
                </div>
                <div class='select_crop_type'>
                    <div class="edit-label">{!! t('Choose an option') !!}</div>
                    <div class="edit-choose-container">
                        <a href="#" class="edit-choose-block" data-type="all">
                            <div class="edit-to-photo">
                                <img src="/img/document-edit-icc-2.svg" alt="Alternate Text">
                                <div class="mini-title-photo">{!! t('All pages same size') !!}</div>
                            </div>
                            <div class="edit-to-text">{!! t('Crop whole document') !!}</div>
                        </a>
                        <a href="#" class="edit-choose-block" data-type="individually">
                            <div class="edit-to-photo">
                                <img src="/img/document-edit-icc-3.svg" alt="Alternate Text">
                                <img style="margin:0 -5px;" src="/img/document-edit-icc-3.svg" alt="Alternate Text">
                                <img src="/img/document-edit-icc-3.svg" alt="Alternate Text">
                                <div class="mini-title-photo">{!! t('Each page different size') !!}</div>
                            </div>
                            <div class="edit-to-text">{!! t('Crop pages individually') !!}</div>
                        </a>
                    </div>
                    <div class="more-options-btns-wrap">
                        <button class="options-btn" type="submit"
                                onclick="$('.edit-choose-block[data-type=\'all\']').click()">{!! t('Continue') !!}</button>
                    </div>
                </div>
            </div>
        </section>

        <section id="preview_section" class="crop-edit hidden">
            <div class="container" id="pages_previews_here">
                <div class="crop-edit-block" id="dummy_page">
                    <div class="crop-edit-top">
                        <div class="crop-edit-form">
                            <label>
                                <span>{!! t('Top') !!}</span>
                                <input type="text" name="name" value="0" placeholder="0"/>
                            </label>
                            <label>
                                <span>{!! t('Right') !!}</span>
                                <input type="text" name="name" value="0" placeholder="0"/>
                            </label>
                            <label>
                                <span>{!! t('Bottom') !!}</span>
                                <input type="text" name="name" value="0" placeholder="0"/>
                            </label>
                            <label>
                                <span>{!! t('Left') !!}</span>
                                <input type="text" name="name" value="0" placeholder="0"/>
                            </label>
                            <span>(inch)</span>
                        </div>
                        <div class="crop-edit-auto">
                            <button class="options-btn auto_crop" type="submit">Auto-crop</button>
                        </div>
                    </div>
                    <div class="crop-edit-photo">
                        Loading...
                    </div>
                </div>
                <div class="crop-text">{!! array_key_exists(7, $PageBlocks) ? $PageBlocks[7] : 'Content outside the crop area is hidden but not completely removed from the document' !!}</div>
            </div>
        </section>

        <section class="fixed-bottom-panel hidden" id="start_crop" style="z-index: 1000000;">
            <form class="fixed-task-form">
                <div class="more-options-btns-wrap">
                    <button class="options-btn" id="crop_pdf"
                            type="button">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Crop PDF' !!}</button>
                </div>
            </form>
        </section>


        @if (count($PageGuides))
            <section class="how-it-works">
            
                @foreach ($PageGuides as $Guide)
                    @if (!is_null($Guide->title))
                        <div class="title-section"><h2>{{ $Guide->title }}</h2></div>
                    @endif

                    @if($ads && $device_is=='phone')
                        @include("ads.adx320x100")
                    @endif

                    <div class="container centered">
                        @if (!is_null($Guide->subtitle))
                            <p class="title-description">{{ $Guide->subtitle }}</p>
                        @endif
                        @if (!is_null($Guide->content))
                            <div class="post">
                                {!! htmlspecialchars_decode($Guide->content) !!}
                            </div>
                        @endif
                    </div>
                @endforeach
            </section>
        @endif

        <div class="upload-top-info">
            <div class="container">
                <div class="app-title">
                    <div class="wrapper">
                        <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to crop your PDF file? Let\'s go!' !!}</h1>
                        <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Trim PDF margins, change PDF page size' !!}</p>
                    </div>
                </div>
                <div class="app-welcome">
                    <form action="#">
                        <div class="upload-img">
                            <img src="/img/pdf-img.svg" alt="">
                        </div>
                        {!! t('<h3>UPLOAD <strong>PDF</strong> FILE</h3>') !!}

                        <div class="upload-button" onclick='$("#drop_zone input[type=file]").click(); return false;'>
						<span>
							{{ t("Upload PDF file") }}
						</span>
                            <input type="file">
                        </div>

                    </form>
                    <div class="upload-welcom-descr">
                        {!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                    </div>
                </div>
            </div>
        </div>

        @include ('inc.result_block_new')
        <?php } ?>
    </main>
@endsection

@section('js')
	<style>
		#pages_previews_here{	
			background: no-repeat;
			box-shadow: none;
		}
		
		.page_outer{
			padding-bottom: 20px;
			background: white;
			margin-bottom: 40px;
		}
	
		.crop-section__page{
			min-height: auto;
		}
	</style>
    <script>
//        jQuery(window).scroll(function () {
//            if (jQuery(window).scrollTop() > 50) {
//                jQuery('.crop-edit-top').addClass('fixed-crop');
//            } else {
//                jQuery('.crop-edit-top').removeClass('fixed-crop');
//            }
//        });
    </script>


@endsection
