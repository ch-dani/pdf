<template>
    <li>
        <a class="tools-menu-item" data-editor-name="annotate" data-annotate-type="strike" href="#">
            <img
                    src="/img/icon-quote.svg"
                    alt="Icon Quote">
            <span>{{ title }}</span>
            <img src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
        </a>
        <ul class="tools-dropdown-menu list-opts annotate-opts">
            <li>
                <a class="hl_strike" data-editor-name="annotate" data-annotate-type="strike" href="#">{{ strikethrough }}</a>
                <img class="ml-auto"
                     src="/img/icon-s.svg" alt="">
            </li>


            <li>
                <a class="hl_underline" data-editor-name="underline" data-annotate-type="underline" href="#">{{ "Underline" }}</a>
                <img class="ml-auto" src="/img/icon-s.svg" alt="">
            </li>
            
            <li>
                <a class="highlight-text hl_higlight" href="#">{{ highlight }}</a>
                <a href="#"><span style="background-color: rgba(243,136,112,0.501);"
                                  data-editor-name="annotate" data-annotate-type="highlight"
                                  class="highlite-color"></span>
                </a>
                <a href="#"><span style="background-color: rgba(240,243,112,0.501);"
                					data-editor-name="annotate" data-annotate-type="highlight"
                                  class="highlite-color"></span>
                </a>
                <a href="#"><span style="background-color: rgba(112,243,133,0.501);"
                					data-editor-name="annotate" data-annotate-type="highlight"
                                  class="highlite-color"></span>
                </a>
                <a href="#"><span style="background-color: rgba(123,112,243,0.501);"
                					data-editor-name="annotate" data-annotate-type="highlight"
                                  class="highlite-color"></span>
                </a>
                <a href="#" style='display: none;'><span class="highlite-color-add"><img 
                		data-editor-name="annotate" data-annotate-type="highlight"
                        src="/img/icon-plus-white-small.svg"
                        alt=""></span>
                </a>
                <img class="ml-auto" src="/img/icon-u.svg" alt="">
            </li>

            <li>
                <a data-editor-name="free_draw" 
                                  class="free_draw_color"
                draw-style="rgba(243,136,112,0.501)"
                href="#">{{ "Free drawing" }}</a>
				<a href="#"><span style="background-color: rgba(243,136,112,0.501);"
                                  data-editor-name="free_draw" 
                                  class="free_draw_color"></span>
                </a>
                <a href="#"><span style="background-color: rgba(240,243,112,0.501);"
                					data-editor-name="free_draw" 
                                  class="free_draw_color"></span>
                </a>
                <a href="#"><span style="background-color: rgba(112,243,133,0.501);"
                					data-editor-name="free_draw" 
                                  class="free_draw_color"></span>
                </a>
                <a href="#"><span style="background-color: rgba(123,112,243,0.501);"
                					data-editor-name="free_draw" 
                                  class="free_draw_color"></span>
                </a>


                
                <img class="ml-auto" src="/img/drawing.png" alt="">
            </li>
            
        </ul>
    </li>

</template>

<script>
    export default {
        data() {
            return {
                title: 'Annotate',
                strikethrough: 'Strikethrough',
                highlight: 'Highlight',
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
                        this[property] = response.data.data;
                    });
            },
        },
        mounted() {
            this.getTranslatedText('Annotate', 'title');
            this.getTranslatedText('Strikethrough', 'strikethrough');
            this.getTranslatedText('Highlight', 'highlight');
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>
