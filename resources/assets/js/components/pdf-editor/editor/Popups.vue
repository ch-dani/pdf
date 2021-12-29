<template>
    <div class="hidde1n">
        <find-replace-modal></find-replace-modal>
        <undo-modal></undo-modal>
        <redo-modal></redo-modal>
        <signature-modal></signature-modal>
    </div>
</template>

<script>

    import FindReplaceModal from './popups/FindReplaceModal.vue';
    import SignatureModal from './popups/SignatureModal.vue';
    import UndoModal from './popups/UndoModal.vue';
    import RedoModal from './popups/RedoModal.vue';

    export default {
        components: {
            FindReplaceModal,
            SignatureModal,
            UndoModal,
            RedoModal
        }
    }
</script>