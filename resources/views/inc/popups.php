<div class="hidde1n">

	<div class="pop-up" id="find-replace-modal" style="display: none;">
		<div class="modal-header">
			<div class="modal-title">Find &amp; Replace</div>
			<div class="close-replace-modal">×</div>
		</div>
		<div class="modal-body">
			<form action="#">
				<div class="form-group">
					<input value="" id="find_text_input" name="findText" placeholder="Find" class="form-control" type="text">
				</div>
				<div class="form-group">
					<input value="" id="replace_text_input" name="replaceText" placeholder="Replace" class="form-control" type="text">
				</div>
				<div class="checkbox">
					<label>
						<input id="find_match_case" name="replaceMatchCase" type="checkbox"> Match case
					</label>
				</div>
				<div class="checkbox">
					<label>
						Found <span style="margin: 0 5px;" class='found_matches'>0</span> matches
					</label>
				</div>
			</form>

		</div>
		<div class="modal-footer">
			<button class="btn btn-replace" type="button" id="start_replace">Replace</button>
			<button class="btn btn-replace-find-next" id="start_replace_and_find_text" type="button">Replace &amp; Find</button>
			<button class="btn btn-find-next" id="start_find_text" type="button">Find</button>
		</div>
	</div>


	<div class="pop-up" id="find-replace-modal" style="display: none;">
		<div class="modal-header">
			<div class="modal-title">Find & Replace</div>
		</div>
		<div class="modal-body">
			<form action="#">
				<div class="form-group">
					<div class="half-column">
						<input value="" name="findText" placeholder="Find" class="form-control" type="text">
					</div>
					<div class="half-column">
						<input value="" name="replaceText" placeholder="Replace" class="form-control" type="text">
					</div>
				</div>
				<div class="checkbox">
					<label>
						<input name="replaceMatchCase" type="checkbox"> Match case
					</label>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button class="btn btn-replace" type="button" disabled="disabled">Replace</button>
			<button class="btn btn-replace-find-next" type="button" disabled="disabled">Replace &amp; Find</button>
			<button class="btn btn-find-next" type="button">Find</button>
		</div>
	</div>

	
	<div data-options='{"touch": false, "autoDimensions": false}' class="pop-up" id="undo-modal" style="display: none; width: 450px;">
		<div class="modal-header">
			<div class="modal-title">Undo changes</div>
		</div>
		<div class="modal-body">
			<div class="alert-info">No changes found</div>
			<div class="undo-table-wrap">
				<table id="undo_table" style=" width: 100%; ">
					<thead>
					</thead>
					<tbody>
						<tr class="example_tr hidden" undo-element-id="%element_id%">
							<td class="check" style="cursor: pointer;">
								<label>
									<input class="revert_item" type="checkbox" value="%element_id%" element-type="%element_type%">
								</label>
							</td>
							<td class="what" style="cursor: pointer;" valign="baseline"><i class="fas fa-%element_type%"></i><span class='undo_text' title="">%action%</span></td>
							<td class="when" style="cursor: pointer;">%time%</td>
							<td class="where" style="cursor: pointer;">%page%</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-find-next " id="time_to_revert" type="button">Revert selected</button>
		</div>
	</div>

    <div data-options='{"touch": false, "autoDimensions": false}' class="pop-up" id="redo-modal" style="display: none; width: 450px;">
		<div class="modal-header">
			<div class="modal-title">Redo changes</div>
		</div>
		<div class="modal-body">
			<div class="alert-info">No changes found</div>
			<div class="undo-table-wrap">
				<table id="undo_table" style=" width: 100%; ">
					<thead>
					</thead>
				
					<tbody>
						<tr class="example_tr hidden" undo-element-id="%element_id%">
							<td class="check" style="cursor: pointer;">
								<label>
									<input class="revert_item" type="checkbox" value="%element_id%" element-type="%element_type%">
								</label>
							</td>
							<td class="what" style="cursor: pointer;" valign="baseline"><i class="fas fa-%element_type%"></i><span class='undo_text' title="">%action%</span></td>
							<td class="when" style="cursor: pointer;">%time%</td>
							<td class="where" style="cursor: pointer;">%page%</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-find-next " id="time_to_redo" type="button">Redo selected</button>
		</div>
	</div>

	<div class="pop-up create-signature-modal" id="draw-modal" style="display: none;">
		<div class="sigtature-block">
			<div class="signature-close">×</div>
			<div class="signature-top">Create signature</div>
			<ul class="signature-btns">
				<li class="signatore-btn-block signatore-btn-active text_sign" data-type='text'>
					<i class="far fa-keyboard"></i>Type
				</li>
				<li class="signatore-btn-block draw_sign" data-type='draw'>
					<i class="fas fa-signature"></i>Draw
				</li>
				<li>
					<i class="far fa-file-image"></i>
					<label>
						<input onclick="jQuery('#new_image_uploader').click(); return false; $('#draw-modal').hide(); return false; " class="signature-file" type="file" name="name" value="">
						<span>image</span>
					</label>
				</li>
			</ul>

			<div class="create-tab-container">
				<div class="create-tab-block create-tab-active" style="display: block;">
					<input class="signature-input" id="sign_text_input" autocomplete="off" type="text" name="name" value="John Smith">
					<div class="signature-wodrds" id="sign_previews">
						<span style="font-family: 'Gamja Flower'; font-size: 35px; display: inline-block;  " class="signaturePreview sign_preview">John Smith</span>
						<span style="font-family: 'Indie Flower'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview first">John Smith</span>
						<span style="font-family: 'Charmonman'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
						<span style="font-family: 'Pacifico'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
						<span style="font-family: 'Gloria Hallelujah'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
						<span style="font-family: 'Amatic SC'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
						<span style="font-family: 'Shadows Into Light'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
						<span style="font-family: 'Dancing Script'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
						<span style="font-family: 'Dokdo'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
						<span style="font-family: 'Permanent Marker'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
						<span style="font-family: 'Patrick Hand'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
						<span style="font-family: 'Courgette'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
					</div>
				</div>
				<div class="create-tab-block" style="display: none;">
					<p class="touchpad-text">Sign your name using your mouse or touchpad.</p>
					<div style="height: 200px; overflow: hidden; border: 1px solid #EAEBF2; position: relative">
						<canvas width="850" height="200" id="sign_draw_canvas" style="position: absolute; top: 0; left: 0;"></canvas>
					    <img id="canvasimg" style="display:none;">
					</div>
					<button class="u-full-width erase_canvas" id="clear"><span aria-hidden="true">×</span> </button>
				</div>

			</div>
			<div class="signature-btn">
				<a id="save_new_sign" class="button-green save_new_sign" href="#">Save</a>
			</div>

			
		</div>
	</div>
	

</div>
