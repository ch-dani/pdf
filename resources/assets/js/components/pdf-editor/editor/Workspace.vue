<template>
    <div class="app-body">
        <div class="app-workspace">
            <div class="page-container">
                <div class="page-between page-between-first">
                    <a href="#" class="insert-page insert_first_page">{{ title }}</a>
                </div>
                <div class="container" style="width: auto; padding: 0;">
                    <div class="page-main-part" style="width: auto;">

                        <edit-menu></edit-menu>

                        <div id="selectable_div"></div>
                        <div id="simplePDFEditor" current_editor="text">
                            <div class="zoom-outer">
            	                <span class="zoom-control zoom-plus"></span>
            	                <input class="range" type="range" min="0.2" max="3" step="0.1" value="1.0" id="zoom_slider" autocomplete="off">
            	                <span class="zoom-control zoom-minus"></span>
                            </div>

                        	<div id="pdf_editor_pages"></div>
                            <div id="viewer" class="pdfViewer eptaview"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import EditMenu from './workspace/EditMenu.vue';
    export default {
        data() {
            return {
                title: 'Insert Page Here',
            }
        },
        components: {
            EditMenu
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
            this.getTranslatedText('Insert Page Here', 'title');
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>
