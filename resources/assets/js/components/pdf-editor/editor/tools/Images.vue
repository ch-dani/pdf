<template>
    <li>
        <a class="tools-menu-item" data-editor-name="images" href="#">
            <img src="/img/icon-image.svg"
                 alt="Icon Image">
            <span>{{ title }}</span>
            <img
                    src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
        </a>
        <ul class="tools-dropdown-menu image-opts user_images">

            <li class="tools-default image-entry example_image user_image">
                <a href="#" class="user_image_outer">
                    <img src="/img/example-png.png" alt="Example image">
                </a>
            </li>

            <li v-for="image in userImages" class="tools-default image-entry user_image" data-image-id="image.id">
                <a href="#" class="user_image_outer">
                    <img :src="'/uploads/'+image.file_name" :alt="image.file_name">
                </a>
                <a href="#" class="delete-image"><i class="fa fa-trash" aria-hidden="true"></i></a>
            </li>

            <li class="divider"></li>
            <li class="upload-link">
                <input style="display: none;" type="file" id="new_image_uploader" accept="image/x-png,image/gif,image/jpeg">
                <a onclick="jQuery('#new_image_uploader').click(); $('#draw-modal').hide(); return false;" href="#">{{ upload_text }}
                </a>
            </li>
        </ul>
    </li>

</template>

<script>
    export default {
        data() {
            return {
                title: 'Images',
                upload_text: 'Upload image',
                userImages: [],
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
            getUserImages() {
                axios
                    .post('/get-user-images', {type: 'Image'})
                    .then((response) => {
                        this.userImages = response.data.data;
                    });
            },
        },
        mounted() {
            this.getTranslatedText('Images', 'title');
            this.getTranslatedText('Upload image', 'upload_text');
            this.getUserImages();
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>