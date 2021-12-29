@extends('layouts.layout')

@section('content')
	<script>
		window.is_fill_and_sign = {{ $is_fill_and_sign?1:0 }}
		window.new_uuid = "{{ $new_uuid }}";
		window.operation_id = "{{ $operation_id }}";
		
		window.is_editor = true;
		
		
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
		        <h1>Online PDF editor<sup>BETA</sup> ?</h1>
		        <p>Edit PDF files for free. Fill & sign PDF</p>
		    </div>
			<div class="edit-label">Choose an option</div>
		    <div class="edit-choose-container">
		        <a href="#" class="edit-choose-block im_filling_out">                 
					<div class="edit-to-photo">
						<img src="/img/document-edit-icc.svg" alt="Alternate Text" />
					</div>
					<div class="edit-to-text">I'm filling out </div>
		        </a>
		        <a href="#" class="edit-choose-block someone_fill_out">
					<div class="edit-to-photo">
						<img src="/img/mail-to-iccon.svg" alt="Alternate Text" />
						<img class="little-iccon" src="img/arrow-to-right.svg" alt="Alternate Text" />
						<img src="/img/document-edit-icc.svg" alt="Alternate Text" />
					</div>
					<div class="edit-to-text">I want someone else to fill out  </div>
		        </a>
		    </div>
		</div>
	</section>

	<section class="editor-request hidden fill_and_sign_block_2">
		<div class="container">
			<div class="pdf-tab">
			    <div class="tab-btns">
			        <div class="tab-btn-block tab-active-btn">Email</div>
			        <div class="tab-btn-block">Link</div>
			    </div>
			    <div class="tab-container">
			        <div class="tab-block" style="display: block;">
			            <div class="pdf-form request-form">
			            	<form id="fill_and_sign_email">
			            		<div class='before_send'>
							        <h6>Request others to fill out by email</h6>
							        <p>We'll send them a link to fill out your document. You'll receive responses by email.</p>
							        <input type="email" required name="recipient_email" value="" placeholder="Recipient's email" />
							        <input type="email" required name="your_email" value="" placeholder="Your email" />
							        <textarea name="note" placeholder="Add a note (optional)"></textarea>
							        <sub>They will have 30 days to fill out the form.
										<span class="input-help tooltip-wrap">
											<span class="tooltiptext">After 30 days the document is automatically deleted from our servers.</span>
											<i class="far fa-question-circle"></i>
										</span>
									</sub>
							        <div class="pdf-form-btns">
							            <button type="submit" class="button-green" href="#">Send request</button>
							        </div>
					            </div>
					            <div class='after_send'>
					            	Done! Request has been sent to <span class='recipient_email'></span>
					            </div>
					            
			                </form>
			            </div>
			        </div>
			        <div class="tab-block fill_sign_link_block" style="display: none;">
			            <div class="pdf-form request-form">
				            <h6>Request others to fill out via your website</h6>
				            <p>
								Post a link on your website to get visitors to fill out your PDF document. <br/>
								You'll receive responses by email.
							</p>
				            <strong>Where should filled out docs be sent to?</strong>
			            	<div class="before">
						        <input type="text" name="link_email" id="fill_link_email" value="" placeholder="Your email" />
						        <div class="pdf-form-btns">
						            <a class="button-green" id="get_fill_link" href="#">Get link</a>
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



    <div id="uploader_section" class='_editor_section @if($exist_file_id) loading @endif'>
        <div class="upload-top-info">
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
                
                
                <div class="app-welcome">
                    <form action="#" id="drop_zone" enctype="multipart/form-data">
                        <div class="upload-img">
                            <img src="/img/pdf-img.svg" alt="">
                        </div>
                        <h3>UPLOAD
                            <strong>PDF</strong>
                            FILE
                        </h3>
                        @include('inc.uploadButton')
                        
                    	@if(!$is_fill_and_sign)
                        <span class="upload-bottom-text">or start with a <a href="#" class="new-pdf">blank document</a></span>
                        @endif

                    </form>
                    <div class="upload-welcom-descr">
                        {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                    </div>
                </div>
            </div>
        </div>

        @if (count($PageGuides))
            <section class="how-it-works">
                @foreach ($PageGuides as $Guide)
                    @if (!is_null($Guide->title))
                        <div class="title-section">
                            <h2>{{ $Guide->title }}</h2>
                        </div>
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
                        <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to edit your files?' !!}</h1>
                        <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Edit PDF files for free. Fill & sign PDF' !!}</p>
                    </div>
                </div>
                <div class="app-welcome">
                    <form action="#">
                        <div class="upload-img">
                            <img src="img/pdf-img.svg" alt="">
                        </div>
                        <h3>UPLOAD
                            <strong>PDF</strong>
                            FILE
                        </h3>
                        @include('inc.uploadButton')
                        <span class="upload-bottom-text">or start with a <a href="#" class="new-pdf">blank document</a></span>
                    </form>
                    <div class="upload-welcom-descr">
                        {!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                    </div>
                </div>
            </div>
        </div>

    </div>


    <input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">






    <div id="app-root" style="display: none;">
        <div class="container">
            <div class="app-tools">
                <div class="wrapper">
                    <ul class="tools-menu">
                        <li>
                            <a class="tools-menu-item" data-editor-name="text" href="#">
                                <img src="/img/icon-text.svg" alt="Icon Text">
                                <span>Text</span>
                            </a>
                        </li>
                        <li>
                            <a class="tools-menu-item" data-editor-name="links" href="#">
                                <img src="/img/icon-link.svg"
                                     alt="Icon Link">
                                <span>Links</span>
                            </a>
                        </li>
                        <li>
                            <a class="tools-menu-item" data-editor-name="forms" href="#">
                                <img src="/img/icon-form.svg"
                                     alt="Icon Form">
                                <span>Forms</span>
                                <img
                                        src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
                            </a>
                            <ul class="tools-dropdown-menu forms-opts">
                                <li class="tools-default">
                                    <a form_element_type="text"
                                       onclick="$('[data-editor-name=\'text\']').click(); return false;" href="#">
                                        <img
                                                src="/img/icon-t.svg" alt="T">
                                    </a>
                                    <a form_element_type="cross" class=" image_form_item" href="#">
                                        <img
                                                src="/img/icon-cross.svg" alt="Cross">
                                    </a>
                                    <a form_element_type="check" class=" image_form_item" href="#">
                                        <img
                                                src="/img/icon-check.svg" alt="Check">
                                    </a>
                                    <a form_element_type="elipse" class=" image_form_item" href="#">
                                        <img
                                                src="/img/icon-elipse.svg" alt="Elipse">
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li class="upload-link">Create new form fields</li>
                                <li class="tools-default change-form-item-tools-wrap">
                                    <a form_element_type="input" class="change_form_item" href="#">
                                        <img
                                                src="/img/form-textfield.svg" alt="input">
                                    </a>
                                    <a form_element_type="textarea" class="change_form_item" href="#">
                                        <img
                                                src="/img/form-textarea.svg" alt="textarea">
                                    </a>
                                    <a form_element_type="radio" class="change_form_item" href="#">
                                        <img
                                                src="/img/icon-elipse.svg" alt="radio">
                                    </a>
                                    <a form_element_type="checkbox" class="change_form_item" href="#">
                                        <img
                                                style="width: 30px;" src="/img/form-checkbox.svg" alt="Elipse">
                                    </a>
                                    <a form_element_type="dropdown" class="change_form_item" href="#">
                                        <img
                                                src="/img/form-dropdown.svg" alt="Elipse">
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="tools-menu-item" data-editor-name="images" href="#">
                                <img src="/img/icon-image.svg"
                                     alt="Icon Image">
                                <span>Images</span>
                                <img
                                        src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
                            </a>
                            <ul class="tools-dropdown-menu image-opts user_images">

                                <li class="tools-default image-entry example_image user_image">
                                    <a href="#" class="user_image_outer">
                                        <img src="/img/example-png.png" alt="Example image">
                                    </a>
                                </li>
                                @foreach ($user_images as $image)
                                    <li class="tools-default image-entry user_image" data-image-id="{{$image['id']}}">
                                        <a href="#" class="user_image_outer">
                                            <img src="/uploads/{{$image['file_name']}}" alt="{{$image['file_name']}}">
                                        </a>
                                        <a href="#" class="delete-image"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </li>
                                @endforeach

                                <li class="divider"></li>
                                <li class="upload-link">
                                    <input style="display: none;" type="file" id="new_image_uploader" accept="image/x-png,image/gif,image/jpeg">
                                    <a onclick="jQuery('#new_image_uploader').click(); $('#draw-modal').hide(); return false;" href="#">Upload
                                        image
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="tools-menu-item" data-editor-name="sign" href="#">
                                <img
                                        src="/img/icon-signature.svg"
                                        alt="Icon Signature">
                                <span>Sign</span>
                                <img
                                        src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
                            </a>
                            <ul class="tools-dropdown-menu sign-opts">
                                <li class="tools-default sign-entry user_image">
                                    <a href="#">
                                        <img id="current_sign" src="/img/sign.svg" alt="">
                                    </a>
                                </li>
                                @foreach ($user_signs as $image)
                                    <li class="tools-default sign-entry user_image" data-image-id="{{$image['id']}}">
                                        <a href="#" class="user_image_outer">
                                            <img src="/uploads/{{$image['file_name']}}" alt="{{$image['file_name']}}">
                                        </a>
                                        <a href="#" class="delete-image"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </li>
                                @endforeach

                                
                                <li class="divider"></li>
                                <li class="upload-link">
                                    <a href="#draw-modal" class="open_draw_modal">New Signature</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="tools-menu-item" data-editor-name="whiteout" href="#">
                                <img
                                        src="/img/icon-eraser.svg"
                                        alt="Icon Eraser">
                                <span>Whiteout</span>
                            </a>
                        </li>
                        <li>
                            <a class="tools-menu-item" data-editor-name="annotate" href="#">
                                <img
                                        src="/img/icon-quote.svg"
                                        alt="Icon Quote">
                                <span>Annotate</span>
                                <img
                                        src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
                            </a>
                            <ul class="tools-dropdown-menu list-opts annotate-opts">
                                <li>
                                    <a class="hl_strike" href="#">Strikethrough</a>
                                    <img class="ml-auto"
                                         src="/img/icon-s.svg" alt="">
                                </li>
                                <li>
                                    <a class="highlight-text hl_higlight" href="#">Highlight</a>
                                    <a href="#"><span style="background-color: rgba(243,136,112,0.501);"
                                                      class="highlite-color"></span>
                                    </a>
                                    <a href="#"><span style="background-color: rgba(240,243,112,0.501);"
                                                      class="highlite-color"></span>
                                    </a>
                                    <a href="#"><span style="background-color: rgba(112,243,133,0.501);"
                                                      class="highlite-color"></span>
                                    </a>
                                    <a href="#"><span style="background-color: rgba(123,112,243,0.501);"
                                                      class="highlite-color"></span>
                                    </a>
                                    <a href="#"><span class="highlite-color-add"><img
                                                    src="/img/icon-plus-white-small.svg"
                                                    alt=""></span>
                                    </a>
                                    <img
                                            class="ml-auto" src="/img/icon-u.svg" alt="">
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="tools-menu-item" href="#">
                                <img    style=" width: 18px; "
                                        src="/img/icon-shapes.svg"
                                        alt="Icon Shapes">
                                <span>Shapes</span>
                                <img
                                        src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
                            </a>
                            <ul class="tools-dropdown-menu list-opts shapes_dropdown">
                                <li>
                                    <a class="sub_menu_item" data-editor-name="rectangle" href="#">Rectangle</a>
                                    <img
                                            class="ml-auto" src="/img/icon-shapes.svg" alt="">
                                </li>
                                <li>
                                    <a class="sub_menu_item" data-editor-name="elipse" href="#">Elipse</a>
                                    <img
                                            class="ml-auto" src="/img/icon-shapes.png" style=" width: 18px; " alt="">
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="tools-menu-item open_undo_modal" href="#" title="Undo" data-editor-name="skip_it">
                                <img src="/img/icon-undo.svg" alt="Arrow Undo">
                                <span>Undo</span>
                            </a>
                        </li>
                        <li>
                            <a class="tools-menu-item open_redo_modal" href="#" title="Redo" data-editor-name="skip_it">
                                <img src="/img/icon-undo.svg" alt="Arrow Redo">
                                <span>Redo</span>
                            </a>
                        </li>
                        <li class="drop_tool_menu">
                            <a class="tools-menu-item tools-menu-item-more" href="#">
                                <img src="/img/icon-arrow-down.svg"
                                     alt="Arrow Down">
                            </a>
                            <ul class="tools-dropdown-menu dropdown-menu-right list-opts">
                                <li>
                                    <a class="open_search_modal" href="#find-replace-modal" data-editor-name="skip_it">Find & Replace</a>
                                    <img
                                            class="ml-auto" src="/img/icon-search.svg" alt="">
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="app-title app-title-editor">
            <div class="wrapper">
                <h1>Online PDF editor</h1>
                <p>Edit PDF files for free. Fill & sign PDF</p>
            </div>
        </div>
        <div class="app-body">
            <div class="app-workspace">
                <div class="page-container">
                    <div class="page-between page-between-first">
                        <a href="#" class="insert-page insert_first_page">Insert Page Here</a>
                    </div>
                    <div class="container" style="width: auto; padding: 0;">
                        <div class="page-main-part" style="width: auto;">
                            @include ('inc.editmenu')
                            <div id="selectable_div"></div>
                            <div id="simplePDFEditor" current_editor="text">
                                <div id="viewer" class="pdfViewer"></div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- 	<div class="app-footer">
        <a href="#" class="apply-changes">Apply Changes <i class="fas fa-chevron-right"></i></a>
        </div> -->
    </div>
    <a href="#" class="scroll-top-arrow"></a>
    <div class="footer-editor">
        <div class="container">
            <div class="footer-editor-item">
                <a class="ft-back-btn" href="#">
                    <img src="/img/icon-back.svg" alt="">
                    Back
                </a>
            </div>
            <div class="footer-editor-item">
                <div class="ft-text-info">
                    <img src="/img/icon-pdf.svg" alt="">
                    <span class='file_name_here'>{{ $file_name }}</span>
                </div>
            </div>
            <div class="footer-editor-item">
                <a class="apply-btn" href="#">
                    <img src="/img/icon-save.svg" alt="">
                    <span class='apply_changes_1'>Apply Changes</span>
                </a>
            </div>
        </div>
    </div>

    <div id="test_result"></div>


    @include ('inc.result_block')

    @include ('inc.popups')
@endsection
