
@extends('layouts.layout')


@section('content')
	<script>
		window.is_fill_and_sign = {{ $is_fill_and_sign?1:0 }}
		window.new_uuid = "{{ $new_uuid }}";
		window.operation_id = "{{ $operation_id }}";
		
		window.is_editor = true;
		
		var new_editor = true;
		
		
	</script>

	@if($open_exist_file)
		<script>
			window.exist_file = "/{{ $open_exist_file }}";
			document.addEventListener("DOMContentLoaded", function(){
				spe.init({container_selector: "simplePDFEditor", external_url: window.exist_file});
			});
		</script>
	@else
		<script>
			window.exist_file = false;
		</script>
	@endif




	@if($is_fill_and_sign)
		<style>
		.drop_tool_menu{
			display: none;
		}

		#simplePDFEditor:not([current_editor='annotate']) .text_content_element:not(.spe_element){
			pointer-events: none !important;
		}
		
		#simplePDFEditor:not([current_editor='annotate']) .text_content_element:not(.spe_element){
		}
	
	</style>



	<div class="upload-top-info hidden fill_and_sign_header">
		<div class="container">
		    <div class="app-title">
		        <div class="wrapper">
		            <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Online PDF editor<sup>BETA</sup>' !!}</h1>
		            <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Edit PDF files for free. Fill & sign PDF' !!}</p>
		            @if(array_key_exists(7, $PageBlocks))
		            <div class="new-container">
		                <a href="#" class="new-block">
		                	{!! $PageBlocks[7] !!}
		                </a>
		            </div>
		            @endif
		        </div>
		    </div>
		</div>

		
	</div>
	
	<section class="new-edit-example hidden fill_and_sign_block_1">
		<div class="app-title">
		    <div class="wrapper">
	            <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Online PDF editor<sup>BETA</sup>' !!}</h1>
	            <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Edit PDF files for free. Fill & sign PDF' !!}</p>
		    </div>
			<div class="edit-label">{{ t("Choose an option") }}</div>
		    <div class="edit-choose-container">
		        <a href="#" class="edit-choose-block im_filling_out">                 
					<div class="edit-to-photo">
						<img src="/img/document-edit-icc.svg" alt="Alternate Text" />
					</div>
					<div class="edit-to-text">{{ t("I'm filling out") }}</div>
		        </a>
		        <a href="#" class="edit-choose-block someone_fill_out">
					<div class="edit-to-photo">
						<img src="/img/mail-to-iccon.svg" alt="Alternate Text" />
						<img class="little-iccon" src="/img/arrow-to-right.svg" alt="Alternate Text" />
						<img src="/img/document-edit-icc.svg" alt="Alternate Text" />
					</div>
					<div class="edit-to-text">{{ t("I want someone else to fill out") }}  </div>
		        </a>
		    </div>
		</div>
	</section>

	<section class="editor-request hidden fill_and_sign_block_2">
		<div class="container">
			<div class="pdf-tab">
			    <div class="tab-btns">
			        <div class="tab-btn-block tab-active-btn">Email</div>
			        <div class="tab-btn-block">{{ t("Link") }}</div>
			    </div>
			    <div class="tab-container">
			        <div class="tab-block" style="display: block;">
			            <div class="pdf-form request-form">
			            	<form id="fill_and_sign_email">
			            		<div class='before_send'>
							        <h6>{!! array_key_exists(8, $PageBlocks) ? $PageBlocks[8] : 'Request others to fill out by email' !!}</h6>
							        <p>{!! array_key_exists(9, $PageBlocks) ? $PageBlocks[9] : "We'll send them a link to fill out your document. You'll receive responses by email." !!}</p>
							        <input type="email" required name="recipient_email" value="" placeholder="Recipient's email" />
							        <input type="email" required name="your_email" value="" placeholder="Your email" />
							        <textarea name="note" placeholder="Add a note (optional)"></textarea>
							        <sub>{!! array_key_exists(10, $PageBlocks) ? $PageBlocks[10] : 'They will have 30 days to fill out the form.' !!}
										<span class="input-help tooltip-wrap">
											<span class="tooltiptext">{!! array_key_exists(11, $PageBlocks) ? $PageBlocks[11] : 'After 30 days the document is automatically deleted from our servers.' !!}</span>
											<i class="far fa-question-circle"></i>
										</span>
									</sub>
							        <div class="pdf-form-btns">
							            <button type="submit" class="button-green" href="#">{{ t("Send request") }}</button>
							        </div>
					            </div>
					            <div class='after_send'>
					            	{!! array_key_exists(12, $PageBlocks) ? $PageBlocks[12] : 'Done! Request has been sent to' !!} <span class='recipient_email'></span>
					            </div>
					            
			                </form>
			            </div>
			        </div>
			        <div class="tab-block fill_sign_link_block" style="display: none;">
			            <div class="pdf-form request-form">
				            <h6>{!! array_key_exists(13, $PageBlocks) ? $PageBlocks[13] : 'Request others to fill out via your website' !!}</h6>
				            <p>
								{!! array_key_exists(14, $PageBlocks) ? $PageBlocks[14] : "Post a link on your website to get visitors to fill out your PDF document. <br/>You'll receive responses by email." !!}
							</p>
				            <strong>{!! array_key_exists(14, $PageBlocks) ? $PageBlocks[14] : 'Where should filled out docs be sent to?' !!}</strong>
			            	<div class="before">
						        <input type="text" name="link_email" id="fill_link_email" value="" placeholder="Your email" />
						        <div class="pdf-form-btns">
						            <a class="button-green" id="get_fill_link" href="#">{{ t("Get link") }}</a>
						        </div>
			                </div>
				            <div class="after hidden">
				            	<textarea class="fill_sign_textarea"></textarea>
				            </div>
			            </div>
			        </div>
			    </div>
			</div>
		</div>
	</section>
	@endif

    {{----}}
    
    <?php
    
     ?>
    

    <div id="overview">
        <uploader 
        :exist-file-id="'{{ $exist_file_id }}'" 
        :page-blocks='{{ json_encode($PageBlocks) }}' 
        ads="{{ $ads }}" 
        device-is="{{ $device_is }}" 
        path="{{ Request::path() }}" 
        is-fill-and-sign="{{ $is_fill_and_sign }}" 
        :page="{{ $PageInfo->id }}" 
        :active-language="{{ $ActiveLanguage->id }}"></uploader>
    </div>
    
    
    <input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">

    <div id="editor-wrap">
        <pdf-editor
         active-language="{{ $ActiveLanguage->id }}" :page-blocks='{{ json_encode($PageBlocks) }}' 
        :file-name="'{{ $file_name }}'" ads="{{ $ads }}" device-is="{{ $device_is }}"></pdf-editor>
    </div>

    <script src="/js/vue/pdf-editor.js"></script>
@endsection
