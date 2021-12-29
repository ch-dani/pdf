<?php include "inc/head.php"; ?>
<?php include "inc/header.php"; ?>

<main>
    <section class="section_top converting">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h30-title title_main">Convert PDF to EPUB Documents (PDF to EPUB)</h2>
                <h3 class="sub-title">Creates a EPUB document from PDF file (.pdf)</h3>
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
                            <img src="img/convert-document-epub.png" alt="">
                        </div>

                    </div>
                    <div class="download_icon_doc"><a href="#"><img src="img/download_arrow.svg"></a></div>

                    <div class="name_doc">
                        <h6>Document 1.pdf</h6>
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
                <div class="link_convert_left">
                    <a href="#" class="link_convert_item">
                        <?php include "img/link_conver-1.svg"; ?>
                        Merge PDF
                    </a>
                    <a href="#" class="link_convert_item">
                        <img src="img/link_conver-2.png" alt="">
                        Compress
                    </a>
                    <a href="#" class="link_convert_item">
                        <?php include "img/link_conver-3.svg"; ?>
                        Remove
                    </a>
                </div>
                <div class="link_convert_right">
                    <a href="#" class="link_convert_item">
                        <img src="img/logos_google-drive.png" alt="">
                        Save to Google Drive
                    </a>
                    <a href="#" class="link_convert_item">
                        <?php include "img/logos_dropbox.svg"; ?>
                        Save to Dropbox
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php include "inc/tools-pd.php"; ?>
    <?php include "inc/banner.php"; ?>


</main>

<?php include "inc/footer.php"; ?>