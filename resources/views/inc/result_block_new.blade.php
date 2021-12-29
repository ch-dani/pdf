
<div class="pop-up" id="apply-popup">
        <div class="modal-header hidden">
            <div class="modal-title"><?php echo t("Your document is ready"); ?></div>
            <div onclick='$("#apply-popup").removeClass("active"); $("#shadow_box").remove(); return false;' class="close-replace-modal">×</div>
        </div>
        <div class="modal-body">
        	<h2 id="file_size_changes" class="hidden" ><?php echo t("We compressed your file from"); ?> <span class='before'></span> to <span class='after'></span> (<span class='compressed_percent'></span>% less)</h2>
        
        	<form class="share_form">
        		<a href="#" id="hide_share_form">
        			<i class="fas fa-arrow-circle-left"></i>
        		</a>
        		<ul class='share_form_tabs'>
        			<li class="like_a_tab active" data-tab="send_by_email"><?php echo t("Send by Email"); ?></li>
        			<li class="like_a_tab" data-tab="share_link"><?php echo t("Share link"); ?></li>
        		</ul>
        		<div class="tab_content" id="send_by_email_tab">
					<form action="#" id="send_mail_r_form">
						<div class='before_send'>
							<div class="form-group">
								<input value="" name="recipient_email" placeholder="Recipient's email" class="form-control" type="email" required>
							</div>
							<div class="form-group">
								<input value="" name="user_email" placeholder="Your email" class="form-control" type="email" required>
							</div>
							<div class="checkbox">
								<label>
									<input type="hidden" name="send_me_copy" value="0">
									<input name="send_me_copy" value="1" type="checkbox">  <?php echo t("Send me a copy as well"); ?>
								</label>
							</div>

							<div class="form-group">
								<textarea name="note" placeholder="Add a note"></textarea>
							</div>
							<div class="form-group">
								<input type="submit" value="Send by email">
							</div>
						</div>
						<div class="after_send">
							<?php echo t("Your email has been sent."); ?> <a href="#" id="send_to_another"><?php echo t("Send another?"); ?></a>
						</div>
						

					</form>
        		
        		</div>
        		<div class="tab_content" id="share_link_tab" style="display: none;">
					<div class="form-group" style="text-align: center;">
						<?php echo t("You'll get a shareable link."); ?><br>
						<?php echo t("Anyone with the link can access the document."); ?>
					</div>
					<div class="form-group" id="textarea_with_link" style="display: none;">
						<textarea></textarea>
					</div>
					<div class="form-group">
						<input type="submit" value="Share" id="create_share_link">
					</div>
        		</div>

        		
        	</form>
        	
        	<div class="result_outer">

				@if($ads && $device_is=='computer')
					@include("ads.adx250x250")
					<style>
						.result_outer{
							display: flex;
							justify-content: center;
						}
					</style>
					
				@endif
        	
		        <div class="download-result">
		            <div class="creating_document">
		            	<h1>
							<span class="fa-3x"><i style="font-size: 30px; margin-right: 10px" class="fas fa-spinner fa-pulse"></i></span>
				        	<span class="current_file_status">
					        	<?php echo t("Your task is processing"); ?>
				        	</span>
				        	
		            	</h1>
		            	<h2 class='current_speed_and_percent'><?php echo t("Please wait..."); ?></h2>
		            </div>
		            <div class="create_file_box" style="display: none;">
		                <div class="result-top-line" style="position: relative;">
							<div style="position: absolute; left: 20px; -moz-transform: scale(1.3); transform: scale(1); " class="fb-like" data-href="https://www.facebook.com/DeftPDF/" data-width="" data-layout="button" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>
		                    <!-- ДЛЯ ПРИМЕРА конец -->
		                    <a href="#" class="download-result-link" target="_blank">
		                        <span class="download_file_name">edited_document.pdf</span>
		                        <b><?php echo t("Download"); ?></b>
		                    </a>
		                </div>
		                <div class="result-bottom-line custom_li_title" style="">
		                    <ul>
		                        <li>
		                        	<span class='custom_tit'><?php echo t("Save to"); ?> Dropbox</span>
		                            <a href="#" class="icon-dropbox" id="save-dropbox" title="Save to Dropbox" data-url="" data-file_name="">Save to Dropbox</a>
		                        </li>
		                        <li>
		                        	<span class='custom_tit'><?php echo t("Save to"); ?> Google Drive</span>
		                            <div class="wrap-gdrive">
		                                <a href="#" class="icon-gdrive" id="icon-gdrive-save"></a>
		                                <div id="savetodrive-div"></div>
		                            </div>
		                        </li>

		                        <li>
		                        	<span class='custom_tit'><?php echo t("Share"); ?></span>
		                            <a href="#" id="show_share_form" class="icon-share" title="Share"><?php echo t("Share"); ?></a>
		                        </li>
		                        <li>
		                        	<span class='custom_tit'><?php echo t("Continue editing with new task"); ?></span>
		                            <a href="#" onclick='$("#apply-popup").removeClass("active"); $("#shadow_box").remove(); return false;' class="icon-continue" title="Continue editing with next task">
		                            <?php echo t("Continue editing with next task"); ?>
		                            </a>
		                        </li>
		                        <li>
		                        	<span class='custom_tit'><?php echo t("Rename file"); ?></span>
		                            <a href="#" id="rename_file" class="icon-rename" title="Rename file"><?php echo t("Rename file"); ?></a>
		                        </li>
		                        <li>
		                        	<span class='custom_tit'><?php echo t("Print file"); ?></span>
		                            <a href="#" class="icon-print" title="Print file"><?php echo t("Print file"); ?></a>
		                        </li>
		                        <li>
		                        	<span class='custom_tit'><?php echo t("Delete file"); ?></span>
		                            <a href="#" onclick="$(window).unbind('beforeunload'); window.location.reload(); return false;" class="icon-delete" title="Delete file"><?php echo t("Delete file"); ?></a>
		                        </li>
		                    </ul>
		                </div>
		            </div>
		        </div>

				@if($ads && $device_is=='computer')
					@include("ads.adx250x250")
				@endif
		        
		    </div>
            <a href="#" onclick='$("#apply-popup").removeClass("active"); $("#shadow_box").remove(); return false;' class="start-over-button"><?php echo t("Start Over"); ?>
            </a>
            <div class='ss' style="text-align: center; font-size: 15px; line-height: 22px;">
				<?php echo t("Does this site help you save time or money?"); ?><br>
				<?php echo t("Say thanks by sharing the website"); ?> :)            
            </div>
            <ul class="result-socials">
                <li>
                    <a target="_blank" href="https://www.facebook.com/sharer.php?u=<?= URL::to('/'); ?>">
                        <img src="/img/soc-facebook.svg" alt="Share on Facebook">
                    </a>
                </li>
                <li>
                    <a target="_blank" href="https://twitter.com/intent/tweet?text=Easy to use Online PDF editor <?= URL::to('/'); ?> @DeftPDF">
                        <img src="/img/soc-twitter.svg" alt="Share on Twitter">
                    </a>
                </li>
                <li>
                    <a target="_blank" href="https://www.youtube.com/channel/UCEPnE2Uq5Q02g2dNrh0_vxQ?sub_confirmation=1">
                        <img src="/img/yt.png" alt="Subscribe">
                    </a>
                </li>

                <li>
                    <a  href="mailto:?&subject=Easy to use Online PDF editor DeftPDF&body=<?= URL::to('/'); ?>">
                        <img src="/img/soc-email.svg" alt="">
                    </a>
                </li>
                <!-- <li>
					<div class="fb-like" data-href="https://facebook.com/deftpdf" data-width="100" data-layout="button" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>                
                </li> -->
                
                <li>
                    <a href="#" onclick="window.open('https://www.linkedin.com/shareArticle?mini=true&url=https%3A//deftpdf.com/&title=Deftpdf&summary=&source=', 'Share', 'width=500,height=300')">
                    <img src="/img/linkedin-button.svg" alt="">
                </a>
					<!-- <script src="https://platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
					<script type="IN/FollowCompany" data-id="19133308" data-counter="bottom"></script>    -->
                </li>
                



            </ul>
        </div>
    </div>
