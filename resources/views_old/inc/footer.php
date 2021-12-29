    <section class="login-modal">
        <div class="login-modal-wrap">
		<div id="closeModal">&times;</div>
            <div class="lolin-modal-block">
                <img src="/img/logo.svg" alt="Alternate Text" />
                <h3>Sign in to your account</h3>
                <form class="sign-form" action="#" method="post">
                    <input type="text" name="name" value="" placeholder="Email"    required/>
                    <input type="text" name="name" value="" placeholder="Password" required />
                    <div class="forgot-password">
                        <a href="#">Forgot password?</a>
                    </div>
                    <button><i class="fas fa-lock" style="margin-right:10px;"></i>Sign in</button>
                </form>
                <div class="alert-danger">You don't have an account with us yet.</div>
                <a class="signed-google" href="#">Sign-up with Google</a>
           	 <p>By logging in with Google you agree to the <a href="/terms">terms</a> and <a href="/policy">privacy policy</a></p>
            </div>
        </div>
    </section>

	<a href="#" id="scroll-top" class="scroll-top"></a>

    <footer>
        <div class="footer-wrap">
            <div class="container">
                <a href="index" class="footer-logo"><img src="/img/logo-footer.svg" alt="Alternate Text" /></a>
                <ul class="footer-menu">
                    <li><a href="#">Pricing & Upgrade</a></li>
                    <li><a href="#">DeftPDF WEB</a></li>
                    <li><a href="#">Developers</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Free for teachers</a></li>
                    <li><a href="#">Press</a></li>
                </ul>
				<div class="switch-language">
				<div class="language-active"><a class="language-link" href="#en"><img src="/img/english.png"/>English</a></div>
				    <ul class="languagepicker">
				    	<li><a class="language-link" href="#nl"><img src="/img/français.png"/>Français</a></li>
				    	<li><a class="language-link" href="#de"><img src="/img/deutsch.png"/>Deutsch</a></li>
				    	<li><a class="language-link" href="#fr"><img src="/img/portugal.png"/>Portugal</a></li>
				    </ul>
			  </div>
            </div>
        </div>

		<canvas id="canvas_for_testing"></canvas>
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-made">
                    <span>Made in Amsterdam </span>©DeftPDF, building PDF tools since 2010.
                </div>
                <div class="footer-info">
                    <a href="#">Cookie Policy</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of use</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- SCRIPTS -->

    <script src="/assets/jquery-1.11.2.js"></script>
    <script src="/libs/jquery-ui/jquery-ui.js"></script>
    <script src="/libs/fancybox/jquery.fancybox.js"></script>
	<script src="/assets/select2/select2.full.min.js"></script>
	<script crossorigin="anonymous" src="https://cburgmer.github.io/rasterizeHTML.js/rasterizeHTML.allinone.js"></script>
	
    <script src="/libs/jquery.selectareas.js"></script>
    <script src="/js/app.js"></script>
    <script src="/js/common.js"></script>
    <script src="/js/JavaScript.js"></script>
	<script src="/libs/pdfjs-dist/build/pdf.js"></script>
	<script src="/libs/pdfjs-dist/web/pdf_viewer.js"></script>
    <script src="/js/simpleviewer.js"></script>


    <!-- SCRIPTS END -->
	</body>
</html>
