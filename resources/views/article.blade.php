@extends('layouts.layout')

@section('content-freeconvert')

<main>

    @php
        $title = json_decode($Article->title, true);
        $content = json_decode($Article->content, true);
        
        $page_title = (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id])) ? $title[$ActiveLanguage->id] : $title[1];
        
    @endphp


    <div class="wrapper_single">
        <div class="container">
            <div class="row">
                <div class="col">

	            	@if($Article->thumbnail)
	                    <div class="main_image_post">
			            	<img src="{{ $Article->thumbnail }}">
	                    </div>
	            	@else
	            	@endif

                    <div class="wrapper_content">
						<h1>{!! (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id])) ? $title[$ActiveLanguage->id] : $title[1] !!}</h1>

			            {!! (isset($content[$ActiveLanguage->id]) and !empty($content[$ActiveLanguage->id])) 
			            ? str_replace("#38;", "&", htmlspecialchars_decode($content[$ActiveLanguage->id])) : 
			            str_replace("#38;", "&", htmlspecialchars_decode($content[1])) !!}
                        
                    </div>
                </div>
            </div>
        </div>
    </div>


	<?php
	//	module__our-blog_2 - для изменения отступа сверху
	$h2_title_post  = t('You may like it');
	$sub_title_post = t('A reliable, intuitive and productive PDF Software');
	$number_posts   = 1;
	?>
	@if(isset($other_posts))
	<section class="module__our-blog <?php if ( isset( $class ) ) { echo $class; } ?> <?php if ( isset( $bg ) ) { echo $bg; } ?>">
		<div class="container">
			<?php if ( isset( $h2_title_post ) ) { ?>
		    <div class="title-wrapper">
				 <h2 class="h2-title title_main"><?php echo $h2_title_post; ?></h2>
				<?php if ( isset( $sub_title_post ) ) { ?> <h3
		                class="sub-title"><?php echo $sub_title_post; ?></h3> <?php } ?>
		    </div>
			<?php } ?>

		    <div class="row">

				@foreach($other_posts as $op)	
					@php
					global $lang_code;
					if($lang_code=='en'){
						$url = "/blog/".$op->url;
					}else{
						$url = "/".$lang_code."/blog/".$op->url;
					}
					@endphp

					@php
					$op_title = json_decode($op->title, 1);
					
					@endphp

		            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
		                <article class="article">

		                    <time class="article__time published" datetime="<?= date("d.m.Y", strtotime($op->created_at)); ?>"><?= date("d.m.Y", strtotime($op->created_at)); ?></time>
		                    <h4 class="article__title entry-title">
		                        <a href="{{$url}}">
									{!! (isset($op_title[$ActiveLanguage->id]) and !empty($op_title[$ActiveLanguage->id])) ? $op_title[$ActiveLanguage->id] : $op_title[1] !!}
		                        </a>
		                    </h4>
				            @if($op->thumbnail)
					            <a href="{{$url}}" title="{{ (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id])) ? $title[$ActiveLanguage->id] : $title[1] }}">
							        <img style="max-height: 140px; width: 100%; object-fit: cover; margin-bottom: 20px;" src="{{$op->thumbnail}}">
							    </a>
				            @endif
		                    
		                    <a class="article__link" href="{{ $url }}">{{ t('Read More') }}</a>
		                </article>
		            </div>
				@endforeach

		    </div>
		</div>
	</section>
	@endif

	@include('page_parts.testimonial') 		                	
	
</main>
@endsection
