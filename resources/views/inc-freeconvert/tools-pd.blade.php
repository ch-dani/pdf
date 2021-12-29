<?php


	if(isset($PageInfo)){
		$page = $PageInfo;
	}

	if(!isset($page)){
		return ;
	}
	
	try{
	    $testimonialsDecoded = json_decode($page->bottom_blocks, true)[$lang_id];
	}catch(ErrorException $e){
		$bbb = $page->bottom_blocks;
		if(!$bbb){
			return ;
		}
		$testimonialsDecoded = json_decode($bbb, true);
		
	}
	
	$blocks = [];
	$flag =false;
	$it = 0;

	foreach($testimonialsDecoded as $td){
		if(!$flag){
			$it++;
			$blocks[$it]['title'] = $td;
		}else{
			$blocks[$it]['content'] = $td;
		}
		$flag = !$flag;
	}
	$blocks = array_filter($blocks);
	$icons = [
	];
	foreach(range(1,6) as $f){
		$cont = (asset('freeconvert/img/about-our-'.$f.'.svg'));
		$icons[$f] = $cont;
	
	}
	
?>

<section class="module__about-tools module bg-white">
    <div class="container">
        <div class="title-wrapper">
            <h2 class="h2-title title_main">{{ t("About our tools") }}</h2>
            <h3 class="sub-title">{{ t('A reliable, intuitive and productive PDF Software') }}</h3>
        </div>
        <div class="row">
        	<?php foreach($blocks as $it=>$block){ ?>
		        <div class="col-12 col-lg-4">
		            <div class="convert about_our">
		            	<img src="<?= ($icons[$it]) ?>">
		            
		                <h4 class="convert__title">
		                	{{ $block['title'] }}
		                </h4>
		                <p class="convert__p">
		                	{{$block['content']}}
		                </p>
		            </div>
		        </div>
            <?php } ?>
            <div class="contact-us">
                <a class="contact-us__button" href="/about-us">{{ t('Learn More') }}</a>
            </div>
        </div>
    </div>
</section>
