<template>
    <div class="tools-dropdown-menu">
        <ul class="color-opts">

            <li v-for="color in defaultColors"><a data-type='set_color' :style="'background-color:'+color" class="color-swatch" href="#"></a></li>

            <li><a style="background-color: #FFFFFF; border-color: #CCC;" class="color-swatch" href="#"></a></li>
            <li>
                <a style="background-color: transparent; border-color: #CCC; position: relative;" class="color-swatch" href="#">
                    <img src="/img/trans-icon.png">
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                defaultColors: []
            }
        },
        methods: {
            getDefaultColors() {
                axios
                    .get('/default-colors')
                    .then((response) => {
                        this.defaultColors = response.data.data;
                    });
            }
        },
        mounted() {
            this.getDefaultColors();
        }
    }
</script>
