<template>
	<div>
		<div id="drawing-mode-options" style="">
			<div class="_draw_grp" style="display: none;">
				<label for="drawing-mode-selector">Mode:</label>
				<select id="drawing-mode-selector">
					<option>Pencil</option>
					<option>Circle</option>
					<option>Spray</option>
				</select>
			</div>
			
			<div class="_draw_grp">
				<label for="drawing-line-width">Line width:</label>
				<input type="range" value="30" min="0" max="150" id="drawing-line-width">
			</div>
			
			<div class="_draw_grp">
				<label for="drawing-color">Line color:</label>
				<input type="color" value="#005E7A" id="drawing-color">
			</div>
		</div>

        <div id="app-root" style="display: none;">
            <tool-panel :active-language="activeLanguage"></tool-panel>
            <app-title :page-blocks="pageBlocks"></app-title>
            <workspace></workspace>
        </div>
        
        <a href="#" class="scroll-top-arrow"></a>

        <footer-editor :file-name="fileName"></footer-editor>

        <div id="test_result"></div>

        <result></result>

        <popups></popups>
	</div>
</template>

<script>

    import ToolPanel from './editor/ToolPanel.vue';
    import AppTitle from './common/AppTitle.vue';
    import Workspace from './editor/Workspace.vue';
    import FooterEditor from './editor/FooterEditor.vue';
    import Result from './editor/Result.vue';
    import Popups from './editor/Popups.vue';

    export default {
        components: {
            ToolPanel,
            AppTitle,
            Workspace,
            FooterEditor,
            Result,
            Popups
        },
        props: [
            'activeLanguage',
            'pageBlocks',
            'fileName',
            'ads',
            'deviceIs',
        ],
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
