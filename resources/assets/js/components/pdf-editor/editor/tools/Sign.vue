<template>
    <li>
        <a class="tools-menu-item" data-editor-name="sign" href="#">
            <img
                    src="/img/icon-signature.svg"
                    alt="Icon Signature">
            <span>{{ title }}</span>
            <img
                    src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
        </a>
        <ul class="tools-dropdown-menu sign-opts">
            <li class="tools-default sign-entry user_image">
                <a href="#" class="user_image_outer">
                    <img id="current_sign"  src="/img/sign.svg" alt="">
                </a>
            </li>

            <li v-for="sign in userSigns" class="tools-default sign-entry user_image" data-image-id="sign.id">
                <a href="#" class="user_image_outer">
                    <img :src="'/uploads/'+sign.file_name" :alt="sign.file_name">
                </a>
                <a href="#" class="delete-image"><i class="fa fa-trash" aria-hidden="true"></i></a>
            </li>

            <li class="divider"></li>
            <li class="upload-link">
                <a href="#draw-modal" class="open_draw_modal">{{ new_signature }}</a>
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                title: 'Sign',
                new_signature: 'New Signature',
                userSigns: [],
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
            getUserSigns() {
                axios
                    .post('/get-user-images', {type: 'Sign'})
                    .then((response) => {
                        this.userSigns = response.data.data;
                    });
            },
        },
        mounted() {
            this.getTranslatedText('Sign', 'title');
            this.getTranslatedText('New Signature', 'new_signature');
            this.getUserSigns();
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>
