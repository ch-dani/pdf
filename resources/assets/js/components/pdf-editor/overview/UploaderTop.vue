<template>
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1 v-html="titleText()"></h1>
                    <p v-html="subTitleText()"></p>
                    <div class="new-container" v-if="existNewText()">
                        <a href="#" class="new-block" v-html="pageBlocksList[7]"></a>
                    </div>
                </div>
            </div>


            <div class="welcome_outer">
                <ads-container v-if="ads && deviceIs=='computer'" :height="250" :width="250"></ads-container>

                <div class="app-welcome">
                    <form action="#" id="drop_zone" enctype="multipart/form-data">
                        <div class="upload-img">
                            <img src="/img/pdf-img.svg" alt="">
                        </div>

                        <h3 v-html="uploadText"></h3>

                        <upload-button :path="path"></upload-button>

                        <span v-if="!isFillAndSign" v-html="uploadBottomText"></span>

                    </form>

                    <div class="upload-welcom-descr" v-html="descText()"></div>
                </div>

                <ads-container v-if="ads && deviceIs=='computer'" :height="250" :width="250"></ads-container>

            </div>
        </div>

        <ads-container v-if="ads && deviceIs=='computer'" :height="90" :width="970"></ads-container>

        <ads-container v-if="ads && deviceIs=='phone'" :height="100" :width="320"></ads-container>
    </div>
</template>

<script>
    import AdsContainer from './AdsContainer.vue'
    import UploadButton from './UploadButton.vue'

    export default {
        props: ['pageBlocks', 'ads', 'deviceIs', 'path', 'isFillAndSign', 'activeLanguage'],
        components: {
            AdsContainer,
            UploadButton
        },
        methods: {
            titleText() {
                return 1 in this.pageBlocksList ? this.pageBlocksList[1] : 'Online PDF editor<sup>BETA</sup>';
            },
            subTitleText() {
                return 2 in this.pageBlocksList ? this.pageBlocksList[2] : 'Edit PDF files for free. Fill & sign PDF';
            },
            descText() {
                return 3 in this.pageBlocksList ? this.pageBlocksList[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.';
            },
            existNewText() {
                return 7 in this.pageBlocksList;
            },
            getUploadText(text, property) {
                axios
                    .post('/translate-phrase', {
                        text: text,
                        active_language: this.activeLanguage,
                    })
                    .then((response) => {
                        this[property] = response.data.data;
                    });
            },
        },
        data() {
            return {
                uploadText: '',
                uploadBottomText: '',
            }
        },
        computed: {
            pageBlocksList() {
                return this.pageBlocks;
            },
        },
        mounted() {
            this.getUploadText('UPLOAD <strong>PDF</strong> FILE', 'uploadText');
            this.getUploadText('<span class="upload-bottom-text">or start with a <a href="#" class="new-pdf">blank document</a></span>', 'uploadBottomText');
        }
    }
</script>