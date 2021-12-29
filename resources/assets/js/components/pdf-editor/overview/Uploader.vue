<template>
    <!--<div id="uploader_section" class='_editor_section @if($exist_file_id) loading @endif'>-->
    <div id="uploader_section" :class="wrapClasses">
        <uploader-top :page-blocks="pageBlocksList" :ads="ads" :device-is="deviceIs" :is-fill-and-sign="isFillAndSign" :active-language="activeLanguage"></uploader-top>
        <page-guides :page="page" :active-language="activeLanguage"></page-guides>
        <uploader-bottom :page-blocks="pageBlocksList" :ads="ads" :device-is="deviceIs" :active-language="activeLanguage"></uploader-bottom>
    </div>
</template>

<script>
    import UploaderTop from './UploaderTop.vue'
    import PageGuides from './PageGuides.vue'
    import UploaderBottom from './UploaderBottom.vue'

    export default {
        components: {
            UploaderTop,
            PageGuides,
            UploaderBottom
        },
        props: [
            'existFileId',
            'pageBlocks',
            'ads',
            'deviceIs',
            'isFillAndSign',
            'pageGuides',
            'page',
            'activeLanguage',
        ],
        mounted() {
            console.log(this.pageGuides);
        },
        computed: {
            wrapClasses() {
                return {
                    _editor_section: true,
                    loading: this.existFileId ? true : false
                }
            },
            pageBlocksList() {
                return this.pageBlocks;
            }
        },
    }
</script>