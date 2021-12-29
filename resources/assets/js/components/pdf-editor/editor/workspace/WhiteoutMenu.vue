<template>
    <div class="whiteout-editable-menu element_editor" data-element-id="false">
        <div class="btn-group-wrap editor_content">
            <div class="btn-group">
                <button class="editable-btn" title="Border">
                    <i class="fa fa-minus"></i>
                </button>
                <ul class="tools-dropdown-menu border-selector">
                    <li class="divider"></li>
                    <li v-for="border in defaultBorders">
                        <a class="set-border" data-type="border" :style="'display: block; height:'+border+'px; background-color: black;'"
                           href="#">
                        </a>
                    </li>
                </ul>
            </div>

            <div class="btn-group change_border_color">
                <button class="editable-btn" title="Border color">
                    <i class="far fa-square"></i>
                </button>
                <colorpicker></colorpicker>
            </div>

            <div class="btn-group change_background_color">
                <button class="editable-btn" title="Bg color">
                    <i class="fas fa-square-full"></i>
                </button>
                <colorpicker></colorpicker>
            </div>

            <div class="btn-group">
                <button class="editable-btn delete_whiteout">
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
                defaultBorders: []
            }
        },
        components: {
            Colorpicker
        },
        methods: {
            getDefaultBorders() {
                axios
                    .get('/default-borders')
                    .then((response) => {
                        this.defaultBorders = response.data.data;
                    });
            }
        },
        mounted() {
            this.getDefaultBorders();
        }
    }
</script>
