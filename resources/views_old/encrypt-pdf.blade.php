@extends('layouts.layout')

@section('content')
    <div class="upload-top-info before_upload">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Encrypt and Protect PDF online' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Protect file with password and permissions' !!}</p>
    			</div>
    		</div>
    		<div class="app-welcome">
    			<form action="#" id="drop_zone">
                	<input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">    			    			
    				<div class="upload-img">
    					<img src="img/pdf-img.svg" alt="">
    				</div>
    				<h3>UPLOAD <strong>PDF</strong> FILE</h3>
    				@include('inc.uploadButton')
    			</form>
    			<div class="upload-welcom-descr">
    				{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
    			</div>
    		</div>
    	</div>
    </div>

	<section class="hidden encrypt-new-edit after_upload" id="encrypt_section">
		<div class="app-title">
			<div class="wrapper">
				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Encrypt and Protect PDF online' !!}</h1>
				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Protect file with password and permissions' !!}</p>
			</div>
			<div class="edit-message">Selected: <span class='file_name_here'></span></div>
		</div>
		<div class="encrypt-form">
			<form class="fixed-task-form" id="encrypt_form">
				<div class="input-field-box">
					<h3 class="crop-title">Password</h3>
				    <div class="head-space">
				        <div class="input-group">
				            <input name="password_open" type="text" placeholder="Example: open123">
				            <div class="input-group-addon">
				                <a class="help-tooltip" target="_blank" href="#">
				                    <i class="far fa-question-circle"></i>
				                    <span class="tooltiptext">This is the password required to open the file</span>
				                </a>
				            </div>
				        </div>
				    </div>
				</div>
				<div class="more-options-box" style="display: none;">
				    <div class="head-space">
						<h3 class="crop-title">Require a password to change permissions</h3>
				        <div class="input-group">
				            <input name="password_own" type="text" placeholder="Example: edit123" value="">
				            <div class="input-group-addon">
				                <a class="help-tooltip" target="_blank" href="#">
				                    <i class="far fa-question-circle"></i>
				                    <span class="tooltiptext">Password required to edit permissions, technically called the 'owner password'</span>
				                </a>
				            </div>
				        </div>
						<div class="encrypt-forms">
							<label>
							    <input type="checkbox" name="allow[]" value="edit"/>
							    <span class="encrypt-span">Modifying</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">Modify the contents of the document by operations other than those controlled by other permissions.</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="copy" />
							    <span class="encrypt-span">Copying text and graphics</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">Copy or otherwise extract text and graphics from the document, including extracting text and graphics.</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="print" />
							    <span class="encrypt-span">Printing</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">Print the document (possibly not at the highest quality level, depending on whether 'High resolution printing' is also selected).</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="highprint" />
							    <span class="encrypt-span">High resolution printing</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">Print the document to a representation from which a faithful digital copy of the PDF content could be generated. When this permission is not selected and only 'Printing' permission is, printing is limited to a low-level representation of the appearance, possibly of degraded quality.</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="editnotes" />
							    <span class="encrypt-span">Filling forms</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">Fill in existing interactive form fields (including signature fields).</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="fillandsign" />
							    <span class="encrypt-span">Commenting</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">Add or modify text annotations, fill in interactive form fields, and, if 'Modifying' permission is also selected, create or modify interactive form fields (including signature fields).</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<label>
							    <input type="checkbox" name="allow[]" value="assemble" />
							    <span class="encrypt-span">Insert, rotate, or delete pages</span>
							    <span class="input-help tooltip-wrap">
							        <span class="tooltiptext">Assemble the document (insert, rotate, or delete pages and create bookmarks or thumbnail images), even if 'Modifying' permission is not selected.</span>
							        <i class="far fa-question-circle"></i>
							    </span>
							</label>
							<div class="encrypt-depending">
								Depending on the software used for opening the PDF file, these permissions may or may not be enforced.
							</div>
							<div class="encrypt-btn">
								<p>Encryption:</p>
								<div class="encrypt-btns-wrap">
									<label class="encrypt-btn-name encrypt-btn-active">
										<input checked type="radio" name="encrypt" value="rc4v2">Medium (RC4v2 128 bits)
									</label>
									<label class="encrypt-btn-name">
										<input type="radio" name="encrypt" value="aesv2">Medium (AES 128 bits)
									</label>
									<label class="encrypt-btn-name">
										<input type="radio" name="encrypt" value="aesv3">Strong (AES 256 bits)
									</label>
								</div>
							</div>					
						</div>							`
				    </div>
				</div>
				<div class="more-options-btns-wrap more-options-btns-wrap-split">
				    <button class="options-btn" type="button" id="start_task">Encrypt PDF ></button>
				    <a href="#" class="options-btn-transparent">More options</a>
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
    					<img src="img/pdf-img.svg" alt="">
    				</div>
    				<h3>UPLOAD <strong>PDF</strong> FILE</h3>
                    @include('includes.upload-button')
    				<span class="upload-bottom-text">or start with a <a href="#" class="new-pdf">blank document</a></span>
    			</form>
    			<div class="upload-welcom-descr">
    				{!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
    			</div>
    		</div>
    	</div>
    </div>

	@include ('inc.result_block')

@endsection
