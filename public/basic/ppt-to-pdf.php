<?php
    include "inc/head.php";
    include "inc/header.php";
?>

<main>

    <section class="section_top">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h30-title title_main">Convert JPG to PDF Documents (JPG to PDF)</h2>
                <h3 class="sub-title">Creates a PDF document from PPT file (.ppt)</h3>
            </div>

            <div class="downloader">
                <div class="downloader__img">
                    <img src="img/convert_1.png" width="250" height="250">
                </div>
                <div class="downloader__upload-wrapper">
                    <div class="downloader__doshed">
                        <div class="downloader__upload">
                            <div class="downloader__icon"><img src="img/doc.svg"></div>
                            <div class="downloader__text">Upload PPT file</div>
                            <div class="downloader__arrow" id="docSelectBtn"><img src="img/arrow-white-down.svg"></div>
                        </div>
                        <div class="downloader__sub-text">or Drop files here</div>
                    </div>
                    <div class="select_wrapper" id="docSelect">
                        <a href='#' class="select_item">
                            <?php include "img/logos_dropbox.svg"; ?>
                            Dropbox
                        </a>

                        <a href='#' class="select_item">
                            <img src="img/logos_google-drive.png" alt="">
                            Google Drive
                        </a>

                        <a href='#' class="select_item">
                            <img src="img/logo-link.png" alt="">
                            Web Address (URL)
                        </a>
                    </div>
                </div>
                <div class="downloader__img">
                    <img src="img/convert_2.png" width="250" height="250">
                </div>
            </div>
        </div>
    </section>

    <section class="module__how-convert module bg-white">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h2-title title_main">How to convert PPT to PDF?</h2>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="convert">
                        <div class="convert__step">1</div>
                        <h4 class="convert__title">Upload your files</h4>
                        <p class="convert__p">To upload your files from your computer, click “Upload PPT File” and select the files you want to edit or drag and drop the files to the page.</p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="convert ">
                        <div class="convert__step">2</div>
                        <h4 class="convert__title">Convert PPT to PDF</h4>
                        <div class="convert-bg"></div>
                        <p class="convert__p">Convert your PPT documents into PDF files by simply clicking “Convert to PDF” and wait for it to be processed.</p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="convert">
                        <div class="convert__step">3</div>
                        <h4 class="convert__title">Download Your PDF Document</h4>
                        <p class="convert__p">Download your file to save it on your computer. You may also save it in your online accounts such as Dropbox or Google Drive, share it via email, print the new document, rename or even continue editing with a new task.</p>
                    </div>
                </div>
                <div class="contact-us">
                    <a class="contact-us__button" href="#">Sign Up</a>
                </div>
            </div>
        </div>
    </section>

<?php
    //include "inc/section_top.php";
    //include "inc/how_to_convert.php";

	$h2_title_post  = 'Our Blog';
	$sub_title_post = 'Non bibendum nisi aliquet non amet lobortis';
	$number_posts   = 2;
	$bg = 'bg-grey';
	include "inc/our_blog.php";
	include "inc/banner.php";
	include "inc/accordion.php";
    include "inc/testimonial.php";
?>

</main>

<?php include "inc/footer.php"; ?>
