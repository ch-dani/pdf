<template>
    <div class="footer-editor">
        <div class="container">
            <div class="footer-editor-item">
                <a class="ft-back-btn" href="#">
                    <img src="/img/icon-back.svg" alt="">
                    {{ back }}
                </a>
            </div>
            <div class="footer-editor-item">
                <div class="ft-text-info">
                    <img src="/img/icon-pdf.svg" alt="">
                    <span class='file_name_here'>{{ fileName }}</span>
                </div>
            </div>
            <div class="footer-editor-item">
                <a class="apply-btn" href="#">
                    <img src="/img/icon-save.svg" alt="">
                    <span class='apply_changes_1'>{{ apply }}</span>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['fileName'],
        data() {
            return {
                back: 'Back',
                apply: 'Apply Changes',
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
            this.getTranslatedText('Back', 'back');
            this.getTranslatedText('Apply Changes', 'apply');
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>