@extends('layouts.layout')

@section('content')
	<script>
		var gdrive_ext = ['epub'];
	</script>
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Unlock PDF Online' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Remove restrictions and password from PDF files' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#" id="drop_zone" class=" before_upload">
            		<input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">    			    			
                    <div class="upload-img">
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD
                        <strong>PDF</strong>
                        FILE
                    </h3>

					<div class="upload-btn-wrap">
						<div class="upload-button">
							<span>
								Upload PDF file
							</span>
							<input class="user_pdf" type="file" accept=".pdf"  >
						</div>
						<button class="dropdown-toggle-btn" type="button">
							<i class="fas fa-caret-down"></i>
						</button>
						<ul class="dropdown-menu-upload">
							<li><a class="drpbox-chooser" href="#" id="drpbox-chooser"><i class="fab fa-dropbox icon"></i> Dropbox</a></li>
							<li><a class="gdrive-chooser" href="#" id="gdrive-chooser"><img class="icon" src="/img/gdrive.png" alt=""> Google Drive</a></li>
							<li><a class="weburl-chooser" href="#"><i class="fas fa-link icon"></i> Web Address (URL)</a></li>
						</ul>
					</div>

                </form>

				<div class="after_upload hidden pdf_section">
					<div class="contact-btn" style="width: 210px; margin: 0 auto;">
						<a class="button-green" id="start_task">Convert PDF to EPUB</a>
					</div>
				</div>



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

    <section class="how-it-works">

        <div class="contact-btn">
            <a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
        </div>

    </section>



    @include ('inc.result_block')
@endsection
