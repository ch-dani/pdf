<section class="module__our-blog <?php if ( isset( $class ) ) { echo $class; } ?> <?php if ( isset( $bg ) ) { echo $bg; } ?>">
    <div class="container">
        <div class="title-wrapper">
            <h2 class="h2-title title_main">{{t("Our Blog")}}</h2>
			<h3 class="sub-title">{{t("Our blog subtitle")}}</h3>
        </div>

        <div class="row">
			<?php 
			$counter=0;
			?>
        	@foreach($blog_items as $Article)

			    @php
					$counter++;
					if($counter>3){
						break; 
					}
			    	global $lang_code;
			        $title = json_decode($Article->title, true);
			        $summary = json_decode($Article->summary, true);
			        $replace = [ "#38;"=>"&", "<p>&</p>"=>"", "& Rsquo;"=>"&rsquo;", "& ldquo; "=>"&ldquo;", "& ldquo;"=>"&ldquo;", " & rdquo;"=>"&rdquo;", "& rdquo;"=>"&rdquo;", "& nbsp;"=>"&nbsp;" ];
			        if($lang_code=='en'){
				        $url = "/blog/".$Article->url;
			        }else{
				        $url = "/".$lang_code."/blog/".$Article->url;
			        }

			    @endphp

		        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
		            <article class="article">

		                <time class="article__time published" datetime="<?= date("d.m.Y H:i:s", strtotime($Article->created_at)); ?>"><?= date("d.m.Y", strtotime($Article->created_at)); ?></time>
		                <h4 class="article__title entry-title">
		                    <a href="{{$url}}">
		                    {{ (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id])) ? $title[$ActiveLanguage->id] : $title[1] }}
		                    </a>
		                    here?
		                </h4>
		                @if($Article->thumbnail)
			                <a href="{{$url}}" title="{{ (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id])) ? $title[$ActiveLanguage->id] : $title[1] }}">
					            <img style="max-height: 140px; width: 100%; object-fit: cover; margin-bottom: 20px;" src="{{$Article->thumbnail}}">
					        </a>
		                @endif


		                <a class="article__link" href="{{$url}}">{{t('Read More')}}</a>
		            </article>
		        </div>
            @endforeach

			<div class="col-12 col-md-12" style="text-align: center !important;" >
					<a class="article__link" href="https://freeconvertpdf.com/blog">{{t('See All')}}</a>
			</div>
			
        </div>
    </div>
</section>

