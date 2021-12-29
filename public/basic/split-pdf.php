<?php include "inc/head.php"; ?>
<?php include "inc/header.php"; ?>
<main>
    <section class="section_top">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h30-title title_main">Online PDF Splitter</h2>
                <h3 class="sub-title">Split individual or multiple pages from a PDF into separate files in the blink of an eye.</h3>
            </div>

            <div class="downloader">
                <div class="downloader__img">
                    <img src="img/convert_1.png" width="250" height="250">
                </div>
                <div class="downloader__upload-wrapper">
                    <div class="downloader__doshed">
                        <div class="downloader__upload">
                            <div class="downloader__icon"><img src="img/doc.svg"></div>
                            <div class="downloader__text">Upload DOC file</div>
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

    <section class="module__how-convert module bg-white pb_5">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h2-title title_main">How to Split PDF Files Online Free</h2>

            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="convert">
                        <div class="convert__step">1</div>
                        <h4 class="convert__title">
                            Select files
                        </h4>
                        <p class="convert__p">
                            Select the PDF files or other documents you wish to split with our PDF merger.
                        </p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="convert ">
                        <div class="convert__step">2</div>
                        <h4 class="convert__title">
                            Split files
                        </h4>
                        <div class="convert-bg"></div>
                        <p class="convert__p">
                            Select the pages you want to extract from the PDF by clicking on them.
                        </p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="convert">
                        <div class="convert__step">3</div>
                        <h4 class="convert__title">
                            Download files
                        </h4>
                        <p class="convert__p">
                            After spliting your PDFs, select and download your splited PDFs to your computer.
                        </p>
                    </div>
                </div>
                <div class="contact-us">
                    <a class="contact-us__button" href="#">Sign Up</a>
                </div>
            </div>
        </div>
    </section>

    <section class="module__how-banner bg-white">
        <div class="container">
            <div class="banner">
                <img src="img/banner.png" width="970" height="250">
            </div>
        </div>
    </section>


</main>

<?php include "inc/footer.php"; ?>
