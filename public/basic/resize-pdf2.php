<?php include "inc/head.php"; ?>
<?php include "inc/header.php"; ?>
<main>
    <section class="crop-section bg-grey">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h30-title title_main">Resize PDF</h2>
                <h3 class="sub-title">Add page margins and padding to change PDF page size</h3>
            </div>

            <div class="crop-section__page"><img src="img/page.png" width="578" height="773"></div>

            <ul class="crop-coordination">
                <li>Top <input type="text" placeholder="123"></li>
                <li>Right <input type="text" placeholder="123"></li>
                <li>Bottom <input type="text" placeholder="123"></li>
                <li>Left <input type="text" placeholder="123"></li>
                <li class="unit">
                    <form action="" method="post" enctype="multipart/form-data" id="form-unit">
                        <div >
                            <button class="unit__button">
                                <span class="unit__unit">px</span>
                              <img src="img/arrow-down-blue.svg"></button>
                            <ul class="unit__sub">
                                <li><button class="unit__btn" type="button" name="px">px</button></li>
                                <li><button class="unit__btn" type="button" name="em">em</button></li>
                                <li><button class="unit__btn" type="button" name="pt">pt</button></li>
                            </ul>
                        </div>
                        <input type="hidden" name="code" value="">
                        <input type="hidden" name="redirect" value="https://drgritz.com.ua/home/">
                    </form>
                </li>
            </ul>

            <div class="contact-us">
                <a class="contact-us__button btn-gradient" href="#"><img src="img/download.svg" width="30" height="30"> Download PDF</a>
            </div>


            <ul class="save">
                <li class="save__li"><a href="#"><img src="img/logo_google-drive.svg" width="26" height="23">Save to Google Drive</a></li>
                <li class="save__li"><a href="#"><img src="img/logo_dropbox.svg" width="28" height="23">Save to Dropbox</a></li>
            </ul>

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
