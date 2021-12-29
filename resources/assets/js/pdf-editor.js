import PdfEditor from './components/pdf-editor/PdfEditor.vue';
import Uploader from './components/pdf-editor/overview/Uploader.vue';

require('./bootstrap');

window.Vue = require('vue');

var pdf_editor = new Vue({
    el: '#editor-wrap',
    components: {
        PdfEditor
    }
});

var pdf_overview = new Vue({
    el: '#overview',
    components: {
        Uploader,
    }
});