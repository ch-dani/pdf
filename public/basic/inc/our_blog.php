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
			<?php
			for ( $i = 1; $i <= $number_posts; $i ++ ) { ?>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                    <article class="article">

                        <time class="article__time published" datetime="2020-02-12 12:00:00">12.02.2020</time>
                        <h4 class="article__title entry-title">
                            <a href="#">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tristique sed nunc
                                pellentesque at nullam.
                                Mattis ac duis cras sed non.
                            </a>
                        </h4>
                        <a class="article__link" href="#">Read More</a>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                    <article class="article">
                        <time class="article__time published" datetime="2020-02-12 12:00:00">12.02.2020</time>
                        <h4 class="article__title entry-title">
                            <a href="#">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</a>
                        </h4>
                        <a class="article__link" href="#">Read More</a>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                    <article class="article">
                        <time class="article__time published" datetime="2020-02-12 12:00:00">12.02.2020</time>
                        <h4 class="article__title entry-title">
                            <a href="#">Lorem ipsum dolor sit amet, consectetur adipiscing elitsed nunc pellentesque at
                                nullam. </a>
                        </h4>
                        <a class="article__link" href="#">Read More</a>
                    </article>
                </div>
				<?php if ( $i == 3 ) { ?>
                    <div class="col-12">
						<?php include "banner.php"; ?>
                    </div>
				<?php } ?>
			<?php } ?>
        </div>
    </div>
</section>

