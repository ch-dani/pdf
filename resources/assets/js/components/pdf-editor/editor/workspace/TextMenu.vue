<template>
    <div class="text-editable-menu" data-element-id="false">
        <div class="btn-group-wrap">
            <div class="btn-group">
                <button class="editable-btn set_bold">
                    <i class="fas fa-bold"></i>
                </button>
            </div>
            <div class="btn-group">
                <button class="editable-btn set_italic">
                    <i class="fas fa-italic"></i>
                </button>
            </div>

            <div class="btn-group">
                <button class="editable-btn set_underline">
                    <i class="fas fa-underline"></i>
                </button>
            </div>
            
            <div class="btn-group">
                <button class="editable-btn">
                    <i class="fas fa-text-height"></i>
                    <img class="caret" src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
                </button>
                <ul class="tools-dropdown-menu font-size-opts">
                    <li>
                        <input class="font-size-number" step="1" min="5" max="100" value="20" name="fontSize2" type="number">
                        <input class="font-size-range" name="fontSize" value="10" max="100" min="5" type="range">
                    </li>
                </ul>
            </div>
            <div class="btn-group">
                <button class="editable-btn">
                    Font
                    <img class="caret" src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
                </button>
                <ul class="tools-dropdown-menu font-family-opts">

                    <li v-if="false">
                        <a href="#">
                            <i class="fas fa-font"></i>
                            <i class="fas fa-plus"></i>
                            More fonts...
                        </a>
                    </li>

                    <li class="divider"></li>

                    <li v-for="font in defaultFonts">
                        <a v-if="typeof(font) == 'object'" class="change_text_font" :data-font-name="font.file" href="#" :style="'font-family:'+font.file">{{ font.title }}</a>
                        <a v-else class="change_text_font" :data-font-name="font" href="#" :style="'font-family:'+font">{{ font }}</a>
                    </li>

                    <li class="divider"></li>
                </ul>
            </div>
            <div class="btn-group">
                <button class="editable-btn">
                    Color
                    <img class="caret" src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
                </button>

                <colorpicker></colorpicker>

            </div>
            <div class="btn-group">
                <button class="editable-btn delete_text">
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>
        </div>
    </div>

</template>

<script>
    import Colorpicker from './Colorpicker.vue';

    export default {
        data() {
            return {
                defaultFonts: []
            }
        },
        components: {
            Colorpicker
        },
        methods: {
            getDefaultFonts() {
                axios
                    .get('/default-fonts')
                    .then((response) => {
                        this.defaultFonts = response.data.data;
                    });
            }
        },
        mounted() {
            this.getDefaultFonts();
        }
    }
</script>

