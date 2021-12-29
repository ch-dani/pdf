@extends('layouts.layout')

@php
	$accept = 'application/pdf';
@endphp

@section('content-freeconvert')
	<main class="file_not_loaded">
		@include('page_parts.toolheader')

		<section id="encrypt_section" class="tool_section crop-section bg-grey after_upload hidden">
			<div class="container">
				<div class="title-wrapper">
					<h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Encrypt and Protect PDF online' !!}</h2>
					<h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Protect PDF file with password and permissions. Add restrictions to your PDF file' !!}</h3>
				</div>

				<div id="pages_previews_here"></div>

				<form class="fixed-task-form" id="encrypt_form">
					<input type="hidden" name="password_own" value="">
					<input type="hidden" name="encrypt" value="rc4v2">
					<input type="hidden" name="pdf_password" value="false">
					<div class="password">
						<h3 class="password__doc file_name_here"></h3>
						<input type="password" placeholder="Set password" class="password__input" name="password_open">
						<a id="start_task" href="#" class="password__send">Protect PDF</a>
					</div>
				</form>

				<div class="link_convert one_item">
					<div class="link_convert_left">
						<a href="#" class="link_convert_item remove">
							@php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
							{{ t("Remove") }}
						</a>
					</div>
				</div>
			</div>
		</section>

		<section class="module__how-convert module bg-white pb_5">
			<div class="container">
				<div class="title-wrapper">
					<h2 class="h2-title title_main">{{ t("How to Protect PDF Files Online Free") }}</h2>
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

		@include('inc-freeconvert.banner')
	</main>
@endsection

@section('js')
	<script src="{{asset('/js/encrypypdf.js')}}"></script>
@endsection

@section('content')
    <div class="upload-top-info before_upload">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Encrypt and Protect PDF online' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Protect file with password and permissions' !!}</p>
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

	<section class="hidden encrypt-new-edit after_upload" id="encrypt_section">
		<div class="app-title">
			<div class="wrapper">
				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Encrypt and Protect PDF online' !!}</h1>
				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Protect file with password and permissions' !!}</p>
			</div>
			<div class="edit-message">{!! t('Selected:') !!} <span class='file_name_here'></span></div>
		</div>
		<div class="encrypt-form">
			<form class="fixed-task-form" id="encrypt_form">
				<div class="input-field-box">
					<h3 class="crop-title">{!! t('Password') !!}</h3>
				    <div class="head-space">
				        <div class="input-group">
				            <input name="password_open" type="text" placeholder="{!! t('Example:') !!} open123">
				            <div class="input-group-addon">
				                <a class="help-tooltip" target="_blank" href="#">
				                    <i class="far fa-question-circle"></i>
				                    <span class="tooltiptext">{!! array_key_exists(7, $PageBlocks) ? $PageBlocks[7] : 'This is the password required to open the file' !!}</span>
				                </a>
				            </div>
				        </div>
				    </div>
				</div>
				<div class="more-options-box" style="display: none;">
				    <div class="head-space">
						<h3 class="crop-title">{!! array_key_exists(8, $PageBlocks) ? $PageBlocks[8] : 'Require a password to change permissions' !!}</h3>
				        <div class="input-group">
				            <input name="password_own" type="text" placeholder="Example: edit123" value="">
				            <div class="input-group-addon">
				                <a class="help-tooltip" target="_blank" href="#">
				                    <i class="far fa-question-circle"></i>
				                    <span class="tooltiptext">{!! array_key_exists(9, $PageBlocks) ? $PageBlocks[9] : "Password required to edit permissions, technically called the 'owner password'" !!}</span>
				                </a>
				            </div>
				        </div>
						<div class="encrypt-forms">
							<label>
							    <input type="checkbox" name="allow[]" value="edit"/>
							    <span class="encrypt-span">{!! t('Modifying') !!}</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">{!! array_key_exists(10, $PageBlocks) ? $PageBlocks[10] : 'Modify the contents of the document by operations other than those controlled by other permissions.' !!}</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="copy" />
							    <span class="encrypt-span">{!! array_key_exists(11, $PageBlocks) ? $PageBlocks[11] : 'Copying text and graphics' !!}</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">{!! array_key_exists(12, $PageBlocks) ? $PageBlocks[12] : 'Copy or otherwise extract text and graphics from the document, including extracting text and graphics.' !!}</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="print" />
							    <span class="encrypt-span">{!! t('Printing') !!}</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">{!! array_key_exists(13, $PageBlocks) ? $PageBlocks[13] : "Print the document (possibly not at the highest quality level, depending on whether 'High resolution printing' is also selected)." !!}</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="highprint" />
							    <span class="encrypt-span">{!! array_key_exists(14, $PageBlocks) ? $PageBlocks[14] : 'High resolution printing' !!}</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">{!! array_key_exists(15, $PageBlocks) ? $PageBlocks[15] : "Print the document to a representation from which a faithful digital copy of the PDF content could be generated. When this permission is not selected and only 'Printing' permission is, printing is limited to a low-level representation of the appearance, possibly of degraded quality." !!}</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="editnotes" />
							    <span class="encrypt-span">{!! array_key_exists(16, $PageBlocks) ? $PageBlocks[16] : 'Filling forms' !!}</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">{!! array_key_exists(17, $PageBlocks) ? $PageBlocks[17] : 'Fill in existing interactive form fields (including signature fields).' !!}</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="fillandsign" />
							    <span class="encrypt-span">{!! array_key_exists(18, $PageBlocks) ? $PageBlocks[18] : 'Commenting' !!}</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">{!! array_key_exists(19, $PageBlocks) ? $PageBlocks[19] : "Add or modify text annotations, fill in interactive form fields, and, if 'Modifying' permission is also selected, create or modify interactive form fields (including signature fields)." !!}</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="assemble" />
							    <span class="encrypt-span">{!! array_key_exists(20, $PageBlocks) ? $PageBlocks[20] : 'Insert, rotate, or delete pages' !!}</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">{!! array_key_exists(21, $PageBlocks) ? $PageBlocks[21] : "Assemble the document (insert, rotate, or delete pages and create bookmarks or thumbnail images), even if 'Modifying' permission is not selected." !!}</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<div class="encrypt-depending">
							{!! array_key_exists(22, $PageBlocks) ? $PageBlocks[22] : "Depending on the software used for opening the PDF file, these permissions may or may not be enforced." !!}
								
							</div>
							<div class="encrypt-btn">
								<p>{!! t('Encryption:') !!}</p>
								<div class="encrypt-btns-wrap">
									<label class="encrypt-btn-name encrypt-btn-active">
										<input checked type="radio" name="encrypt" value="rc4v2">{!! t('Medium:') !!} (RC4v2 128 bits)
									</label>
									<label class="encrypt-btn-name">
										<input type="radio" name="encrypt" value="aesv2">{!! t('Medium:') !!} (AES 128 bits)
									</label>
									<label class="encrypt-btn-name">
										<input type="radio" name="encrypt" value="aesv3">{!! t('Strong:') !!} (AES 256 bits)
									</label>
								</div>
							</div>					
						</div>							`
				    </div>
				</div>
				<div class="more-options-btns-wrap more-options-btns-wrap-split">
				    <button class="options-btn" type="button" id="start_task">{!! t('Encrypt PDF >') !!}</button>
				    <a href="#" class="options-btn-transparent">{!! t('More options') !!}</a>
				</div>
			</form>
		</div>
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
    				<h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to password protect your PDF?' !!}</h1>
    				<p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Protect file with password and permissions' !!}</p>
    			</div>
    		</div>
    		<div class="app-welcome">
    			<form action="#">
    				<div class="upload-img">
    					<img src="/img/pdf-img.svg" alt="">
    				</div>
    				<h3>UPLOAD <strong>PDF</strong> FILE</h3>

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

@endsection
