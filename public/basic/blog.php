<?php include "inc/head.php"; ?>
<?php include "inc/header.php"; ?>

<main>
    <!--    Верхний блок под шапкой, только текст-->
	<?php
	$h2_title  = 'The FreeConvert PDF Blog';
	$sub_title = 'We write about being productive with PDF files';
	include "inc/section_top_only_text.php"; ?>

    <section class="filter">
        <div class="container">
            <div class="row">
                <div class="col">
                    <form action="" class="module__filter">
                        <div class="left_box">
                            <label class="module__check item">
                                <input type="radio" name="filter" checked>
                                <span class="text">All</span>
                            </label>
                            <label class="module__check item">
                                <input type="radio" name="filter">
                                <span class="text">About Tools</span>
                            </label>
                            <label class="module__check item">
                                <input type="radio" name="filter">
                                <span class="text">News</span>
                            </label>
                            <label class="module__check item">
                                <input type="radio" name="filter">
                                <span class="text">How it works</span>
                            </label>
                        </div>
                        <div class="right_box">
                            <input type="text" placeholder="Search" class="input_standart icon_left">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

	<?php
	//	module__our-blog_2 - для изменения отступа сверху
	$class        = 'module__our-blog_2';
	$number_posts = 6;
	include "inc/our_blog.php"; ?>

    <section class="pagination">
        <div class="container">
            <div class="row">
                <div class="col">
                    <ul class="module__pagination">
                        <li class="previous_page no_active">
                            <a href="#">Previous Page</a>
                        </li>
                        <div class="wrap_page">
                            <li class="page">
                                <a href="#">1</a>
                            </li>
                            <li class="page">
                                <a href="#">2</a>
                            </li>
                            <li class="page">
                                <a href="#">3</a>
                            </li>
                            <li class="page separator">
                                <a href="#">...</a>
                            </li>
                            <li class="page">
                                <a href="#">6</a>
                            </li>
                            <li class="page">
                                <a href="#">7</a>
                            </li>
                            <li class="page">
                                <a href="#">8</a>
                            </li>
                        </div>
                        <li class="next_page">
                            <a href="#">Next Page</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

	<?php include "inc/banner.php"; ?>
	<?php include "inc/testimonial.php"; ?>
</main>

<?php include "inc/footer.php"; ?>
