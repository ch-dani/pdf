@extends('layouts.layout')

@section('content-freeconvert')
<script src="{{ asset('js/blog.js') }}"></script>
<script>
	var total_pages = {{ $Articles->lastPage() }};
	var all_total_pages = {{ $Articles->lastPage() }};
</script>


<style>
	.blocked:after{
		content: "";
		background: rgba(255, 255, 255, 0.51);
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
	}

	img[src='/img/blank.png']{

		background: linear-gradient(68.92deg, #4298E8 0%, #8044DB 100%), #FFFFFF;
		width: 360px !important;
		height: 160px !important;
		display: block;
		object-fit: contain;
	}			
	.search{
		position: relative;
	}
	.blog_search{
		position: absolute;
		top: 0;
		right: 0;
		height: 100%;				
	}
</style>    
    
<main>
	@include('page_parts.page_top_part') 


    <section class="filter blog-section">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div action="" class="module__filter">
                        <div class="left_box" id="list-cat">


                            <label class="module__check item">
                                <input type="radio" name="filter" checked>
                                <span data-id="all" class="text change_blog_category">{{ t("All posts") }}</span>
                            </label>

							@if(isset($blog_categories))
								@foreach($blog_categories as $bc)
				                    <label class="module__check item">
				                        <input type="radio" name="filter">
				                        <span data-id="{{ $bc->id }}" class="text change_blog_category">{{ json_decode($bc->title, 1)[1] }}</span>
				                    </label>
								    
							    @endforeach
						    @endif

                        </div>
                        <div class="right_box">
							<form class="search" method="get" action="" style="">
	                            <input name="s" id="search_input" autocomplete="off" type="text" placeholder="Search" class="input_standart icon_left">
							</form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


	<?php 
	$class        = 'module__our-blog_2';
	$number_posts = 6;	
	
	 ?>







	<section class="posts module__our-blog <?php if ( isset( $class ) ) { echo $class; } ?> <?php if ( isset( $bg ) ) { echo $bg; } ?>">
		<div class="container">
			<?php if ( isset( $h2_title_post ) ) { ?>
		    <div class="title-wrapper">
				 <h2 class="h2-title title_main"><?php echo $h2_title_post; ?></h2>
				<?php if ( isset( $sub_title_post ) ) { ?> <h3
		                class="sub-title"><?php echo $sub_title_post; ?></h3> <?php } ?>
		    </div>
			<?php } ?>

		    <div class="row">

				@if(!$Articles->total())
					<article style="width: 100%;
						text-align: center;
						padding: 20px;
						font-size: 20px;">
						{{ t("Not found") }}
					</article>
				@endif
				



				@foreach ($Articles as $i=>$Article)

				<?php 
				
				?>


				    @php
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
				    

					<?php
						
						
//						exit("x");
//						
//						$cont = (isset($summary[$ActiveLanguage->id]) and !empty($summary[$ActiveLanguage->id])) ? 
//		            	str_replace("#38;", "&", htmlspecialchars_decode($summary[$ActiveLanguage->id])) : 
//		            	str_replace("#38;", "&", htmlspecialchars_decode($summary[1]));
//						
//						foreach($replace as $from=>$to){
//							$cont = str_replace($from, $to, $cont);
//						}
//						$cont = \App\Blog::getExcerpt($cont, 15);
					?>


		            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
		                <article class="article">
		                	@if(false)
						    	@if($Article->thumbnail)
							    	<img src="{{ $Article->thumbnail }}">
						    	@else
							    	<img src="/img/blank.png" alt=" ">
						    	@endif
				        	@endif
		                    <time class="article__time published" datetime="<?= date("d.m.Y H:i:s", strtotime($Article->created_at)); ?>">
		                    	<?= date("d.m.Y", strtotime($Article->created_at)); ?>
		                    </time>
		                    <h4 class="article__title entry-title">
		                        <a href="{{$url}}">{{ (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id])) ? $title[$ActiveLanguage->id] : $title[1] }}
		                        </a>
		                    </h4>
				            @if($Article->thumbnail)
					            <a href="{{$url}}" title="{{ (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id])) ? $title[$ActiveLanguage->id] : $title[1] }}">
							        <img style="max-height: 140px; width: 100%; object-fit: cover; margin-bottom: 20px;" src="{{$Article->thumbnail}}">
							    </a>
				            @endif		                    
		                    <a class="article__link" href="{{$url}}">{{ t('Read More') }}</a>
		                </article>
		            </div>
		            


					<?php if ( $i == 5 ) { ?>
		                <div class="col-12">
							@include('page_parts.banner') 		                	
		                </div>
					<?php } ?>

				@endforeach            


				<div id="blog_loading" class="hidden1" style="display: none;">
					Loading...<br>
				</div>
				
				<style>
					#blog_loading, .blog_bottom_info{
						height: 100px;
						background: white;
						text-align: center;
						font-size: 20px;
						padding-top: 40px;
						
					}
				</style>



		    </div>
		</div>
	</section>







	
</main>

	

@endsection

