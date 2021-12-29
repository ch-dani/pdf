<?php include "inc/head.php"; ?>
<?php include "inc/header.php"; ?>

    <main>
        <section class="section_top converting">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">Delete PDF Pages</h2>
                    <h3 class="sub-title">Remove pages from a PDF document.</h3>
                </div>
                <div class="convert_docs_wrapper">
                    <div class="convert_doc left_doc">
                        <div class="convert_doc_content">
                            <h4 class="title_convert_doc">Choose file</h4>
                            <div class="icon_add_doc">
                                <img src="img/icon-add-file.png" alt="">
                                <div class="icon_add_select" id="docSelectBtn">
                                    <?php include "img/icon-add-file-arr.svg"; ?>
                                </div>
                            </div>
                            <h5 class="sub_title_convert_doc">or drop files here</h5>

                        </div>
                        <div class="select_wrapper" id="docSelect">
                            <a href="#" class="select_item">
                                <?php include "img/logos_dropbox.svg"; ?>
                                Dropbox
                            </a>

                            <a href="#" class="select_item">
                                <img src="img/logos_google-drive.png" alt="">
                                Google Drive
                            </a>


                            <a href="#" class="select_item">
                                <?php include "img/logos_dropbox.svg"; ?>
                                Web Address (URL)
                            </a>

                        </div>
                    </div>
                    <div class="convert_doc right_doc">
                        <div class="convert_doc_content">
                            <div class="download_convert_doc">
                                <img src="img/convert-document.png" alt="">
                            </div>

                        </div>
                        <div class="download_icon_doc"><a href="#"><img src="img/download_arrow.svg"></a></div>

                        <div class="name_doc">
                            <h6>Document 1.xls</h6>
                        </div>
                    </div>

                </div>
                <div class="downloader">
                    <div class="downloader__upload">
                        <div class="downloader__icon"><img src="img/download_arrow.svg"></div>
                        <div class="downloader__text">Download PDF</div>
                        <div class="downloader__arrow"></div>
                    </div>
                </div>
                <div class="link_convert">
                    <ul class="save">
                        <li class="save__li"><a href="#"><img src="img/logos_google-drive.png" width="28" height="23">Save to Google Drive</a></li>
                        <li class="save__li"><a href="#"><img src="img/logo_dropbox.svg" width="28" height="23">Save to Dropbox</a></li>
                    </ul>

                </div>

            </div>
        </section>
        <?php include "inc/banner.php"; ?>


    </main>

<?php include "inc/footer.php"; ?>