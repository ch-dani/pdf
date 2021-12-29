<template>
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1 v-html="titleText()"></h1>
                    <p v-html="subTitleText()"></p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="/img/pdf-img.svg" alt="">
                    </div>
                    <h3 v-html="uploadText"></h3>
                    <upload-button :path="path"></upload-button>
                    <span v-html="uploadBottomText"></span>
                </form>
                <div class="upload-welcom-descr" v-html="descText()"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import AdsContainer from './AdsContainer.vue'
    import UploadButton from './UploadButton.vue'

    export default {
        props: ['pageBlocks', 'ads', 'deviceIs', 'path', 'activeLanguage'],
        components: {
            AdsContainer,
            UploadButton
        },
        methods: {
            titleText() {
                return 4 in this.pageBlocksList ? this.pageBlocksList[4] : 'Ready to edit your files?';
            },
            subTitleText() {
                return 5 in this.pageBlocksList ? this.pageBlocksList[5] : 'Edit PDF files for free. Fill & sign PDF';
            },
            descText() {
                return 6 in this.pageBlocksList ? this.pageBlocksList[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.';
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
            this.getUploadText('<h3>UPLOAD <strong>PDF</strong>FILE</h3>', 'uploadText');
            this.getUploadText('<span class="upload-bottom-text">or start with a <a href="#" class="new-pdf">blank document</a></span>', 'uploadBottomText');
        }
    }
</script>