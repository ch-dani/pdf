<template>
    <div class="app-title app-title-editor">
        <div class="wrapper">
            <h1 v-html="titleText()"></h1>
            <p v-html="subTitleText()"></p>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['pageBlocks'],
        methods: {
            titleText() {
                return 1 in this.pageBlocks ? this.pageBlocks[1] : 'Online PDF editor<sup>BETA</sup>';
            },
            subTitleText() {
                return 2 in this.pageBlocks ? this.pageBlocks[2] : 'Edit PDF files for free. Fill & sign PDF';
            },
        }
    }
</script>