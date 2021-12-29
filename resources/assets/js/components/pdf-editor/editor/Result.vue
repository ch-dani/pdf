<template>

    <div class="pop-up" id="apply-popup">
        <div class="modal-header hidden">
            <div class="modal-title">{{ t.ready_text }}</div>
            <div onclick='$("#apply-popup").removeClass("active"); $("#shadow_box").remove(); return false;' class="close-replace-modal">×</div>
        </div>
        <div class="modal-body">
            <h2 id="file_size_changes" class="hidden" >{{ t.compressed_file }} <span class='before'></span> to <span class='after'></span> (<span class='compressed_percent'></span>% less)</h2>

            <form class="share_form">
                <a href="#" id="hide_share_form">
                    <i class="fas fa-arrow-circle-left"></i>
                </a>
                <ul class='share_form_tabs'>
                    <li class="like_a_tab active" data-tab="send_by_email">{{ t.send_by_email }}</li>
                    <li class="like_a_tab" data-tab="share_link">{{ t.share_link }}</li>
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
                                    <input name="send_me_copy" value="1" type="checkbox">  {{ t.send_me_copy }}
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
                            {{ t.email_sent }} <a href="#" id="send_to_another">{{ t.send_another }}</a>
                        </div>
                    </form>
                </div>
                <div class="tab_content" id="share_link_tab" style="display: none;">
                    <div class="form-group" style="text-align: center;">
                        {{ t.shareable_link }}<br>
                        {{ t.anyone_access }}
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

                <ads-container v-if="parentProps.ads && parentProps.deviceIs=='computer'" :height="250" :width="250"></ads-container>

                <div class="download-result">
                    <div class="creating_document">
                        <h1>
                            <span class="fa-3x"><i style="font-size: 30px; margin-right: 10px" class="fas fa-spinner fa-pulse"></i></span>
                            <span class="current_file_status">
					        	{{ t.task_processing }}
				        	</span>

                        </h1>
                        <h2 class='current_speed_and_percent'>{{ t.please_wait }}</h2>
                    </div>
                    <div class="create_file_box" style="display: none;">
                        <div class="result-top-line" style="position: relative;">
                            <div style="position: absolute; left: 20px; -moz-transform: scale(1.3); transform: scale(1); " class="fb-like" data-href="https://www.facebook.com/DeftPDF/" data-width="" data-layout="button" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>
                            <!-- ДЛЯ ПРИМЕРА конец -->
                            <a href="#" class="download-result-link" target="_blank">
                                <span class="download_file_name">edited_document.pdf</span>
                                <b>{{ t.download }}</b>
                            </a>
                        </div>
                        <div class="result-bottom-line custom_li_title" style="">
                            <ul>
                                <li>
                                    <span class='custom_tit'>{{ t.save_to }} Dropbox</span>
                                    <a href="#" class="icon-dropbox" id="save-dropbox" title="Save to Dropbox" data-url="" data-file_name="">Save to Dropbox</a>
                                </li>
                                <li>
                                    <span class='custom_tit'>{{ t.save_to }} Google Drive</span>
                                    <div class="wrap-gdrive">
                                        <a href="#" class="icon-gdrive" id="icon-gdrive-save"></a>
                                        <div id="savetodrive-div"></div>
                                    </div>
                                </li>

                                <li>
                                    <span class='custom_tit'>{{ t.share }}</span>
                                    <a href="#" id="show_share_form" class="icon-share" title="Share">{{ t.share }}</a>
                                </li>
                                <li>
                                    <span class='custom_tit'>{{ t.continue_edit }}</span>
                                    <a href="#" onclick='$("#apply-popup").removeClass("active"); $("#shadow_box").remove(); return false;' class="icon-continue" title="Continue editing with next task">
                                        {{ t.continue_edit }}
                                    </a>
                                </li>
                                <li>
                                    <span class='custom_tit'>{{ t.rename_file }}</span>
                                    <a href="#" id="rename_file" class="icon-rename" title="Rename file">{{ t.rename_file }}</a>
                                </li>
                                <li>
                                    <span class='custom_tit'>{{ t.print_file }}</span>
                                    <a href="#" class="icon-print" title="Print file">{{ t.print_file }}</a>
                                </li>
                                <li>
                                    <span class='custom_tit'>{{ t.delete_file }}</span>
                                    <a href="#" onclick="$(window).unbind('beforeunload'); window.location.reload(); return false;" class="icon-delete" title="Delete file">{{ t.delete_file }}></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


                <ads-container v-if="parentProps.ads && parentProps.deviceIs=='computer'" :height="250" :width="250"></ads-container>
            </div>
            <a href="#" onclick='$("#apply-popup").removeClass("active"); $("#shadow_box").remove(); return false;' class="start-over-button">{{ t.start_over }}
            </a>
            <div class='ss' style="text-align: center; font-size: 15px; line-height: 22px;">
                {{ t.save_time_or_money }}<br>
                {{ t.say_thanks }} :)
            </div>
            <ul class="result-socials">
                <li>
                    <a target="_blank" :href="'https://www.facebook.com/sharer.php?u='+config.site_url">
                        <img src="/img/soc-facebook.svg" alt="Share on Facebook">
                    </a>
                </li>
                <li>
                    <a target="_blank" :href="'https://twitter.com/intent/tweet?text=Easy to use Online PDF editor '+config.site_url+' @DeftPDF'">
                        <img src="/img/soc-twitter.svg" alt="Share on Twitter">
                    </a>
                </li>
                <li>
                    <a target="_blank" href="https://www.youtube.com/channel/UCEPnE2Uq5Q02g2dNrh0_vxQ?sub_confirmation=1">
                        <img src="/img/yt.png" alt="Subscribe">
                    </a>
                </li>
                <li>
                    <a  :href="'mailto:?&subject=Easy to use Online PDF editor DeftPDF&body='+config.site_url">
                        <img src="/img/soc-email.svg" alt="">
                    </a>
                </li>
                <li>
                    <a href="#" onclick="window.open('https://www.linkedin.com/shareArticle?mini=true&url=https%3A//deftpdf.com/&title=Deftpdf&summary=&source=', 'Share', 'width=500,height=300')">
                        <img src="/img/linkedin-button.svg" alt="">
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>

    import AdsContainer from './../overview/AdsContainer.vue'
    import config from './../config.js'

    export default {
        components: {
            AdsContainer
        },
        data() {
            return {
                config: config,
                t: {
                    ready_text: 'Your document is ready',
                    compressed_file: 'We compressed your file from',
                    send_by_email: 'Send by Email',
                    share_link: 'Share link',
                    send_me_copy: 'Send me a copy as well',
                    email_sent: 'Your email has been sent.',
                    send_another: 'Send another?',
                    shareable_link: "You'll get a shareable link.",
                    anyone_access: "Anyone with the link can access the document.",
                    task_processing: "Your task is processing",
                    please_wait: "Please wait...",
                    download: "Download",
                    save_to: "Save to",
                    share: "Share",
                    continue_edit: "Continue editing with new task",
                    rename_file: "Rename File",
                    print_file: "Print File",
                    delete_file: "Delete File",
                    start_over: "Start Over",
                    save_time_or_money: "Does this site help you save time or money",
                    say_thanks: "Say thanks by sharing the website",
                }
            }
        },
        methods: {
            getTranslatedText(text, property) {
                axios
                    .post('/translate-phrase', {
                        text: text,
                        active_language: this.parentProps.activeLanguage
                    })
                    .then((response) => {
                        this.t[property] = response.data.data;
                    });
            },
        },
        mounted() {
            let $this = this;

            $.each(this.t, function (name, text) {
                $this.getTranslatedText(text, name);
            });

            console.log(config);
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>