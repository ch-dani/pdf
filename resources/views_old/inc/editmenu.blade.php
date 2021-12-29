<div class="text-editable-menu" data-element-id="false">
	<div class="btn-group-wrap">
		<div class="btn-group">
			<button class="editable-btn set_bold">
				<i class="fas fa-bold"></i>
			</button>
		</div>
		<div class="btn-group">
			<button class="editable-btn set_italic">
			<i class="fas fa-italic"></i>
			</button>
		</div>
		<div class="btn-group">
			<button class="editable-btn">
			<i class="fas fa-text-height"></i>
			<img class="caret" src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
			</button>
			<ul class="tools-dropdown-menu font-size-opts">
				<li>
					<input class="font-size-number" step="1" min="5" max="100" value="20" name="fontSize2" type="number">
					<input class="font-size-range" name="fontSize" value="10" max="100" min="5" type="range">
				</li>
			</ul>
		</div>
		<div class="btn-group">
			<button class="editable-btn">
			Font
			<img class="caret" src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
			</button>
			<ul class="tools-dropdown-menu font-family-opts">
				<?php if(false){ ?>
				<li>
					<a href="#">
					<i class="fas fa-font"></i>
					<i class="fas fa-plus"></i> 
					More fonts...
					</a>
				</li>
				<?php } ?>
				<li class="divider"></li>
				@foreach($default_fonts as $font)
					<li>
						@if(is_array($font))
							<a class="change_text_font" data-font-name="{{$font['file']}}" href="#" style="font-family: '{{$font['file']}}'">{{$font['title']}}</a>
						@else
							<a class="change_text_font" data-font-name="{{$font}}" href="#" style="font-family: '{{$font}}'">{{$font}}</a>
						@endif
					</li>
				@endforeach
				<li class="divider"></li>
				
			</ul>
		</div>
		<div class="btn-group">
			<button class="editable-btn">
			Color
			<img class="caret" src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
			</button>
			@include ('inc.colorpicker')
		</div>
		<div class="btn-group">
			<button class="editable-btn delete_text">
				<i class="far fa-trash-alt"></i>
			</button>
		</div>
	</div>
</div>





<div class="image-editable-menu" data-element-id="false">
	<div class="btn-group-wrap">
		<div class="btn-group">
			<button class="editable-btn rotate">
				<i class="fas fa-sync"></i>
			</button>
		</div>
		
		<div class="btn-group">
			<button class="editable-btn delete">
				<i class="far fa-trash-alt"></i>
			</button>
		</div>
	</div>
</div>



<div class="link-editable-menu element_editor ff-editable-menu" data-element-id="false">
	<button class="close" type="button" onclick="jQuery('.link-editable-menu').hide(); return false;"><span>×</span></button>

	<div class="">
		<div class="form-group">
			Link properties
		</div>
		<div class="form-group" style="display: none;">
			<label for="external_title">Title</label>
			<input type="text" data-target='title' id="external_title" class="external_title" value="" placeholder="title">
		</div>
		<div class="form-group">
			<label>
				<input checked type="radio" name="link_type" class="link_type" value="1" checked="checked" autocomplete="off">
				Link to external URL
			</label>
		</div>
		<div class="form-group">
			<label for="external_title">URL</label>
			<input type="text" data-target='url' class="external_link" value="" placeholder="Link">
		</div>


		<div class="form-group">
			<label>
				<input checked type="radio" name="link_type" class="link_type" value="2" autocomplete="off">
				Link to internal page
			</label>
		</div>
		<div class="form-group">
			<label for="internal_link">URL</label>
			<input type="text" id="internal_link" data-target='url' class="internal_link" value="" placeholder="Link">
		</div>

		<div class="form-group bottom-btns">
			<div class="btn-group">
				<button class="clone" type="button"><i class="far fa-clone"></i></button>
				<button class="delete"  type="button"><i class="far fa-trash-alt"></i></button>
			</div>
		</div>
		

		<?php if(false){ ?>
			<div class="link_edit_outer">
				<div class="link_type">
					<label>
						<input checked type="radio" name="link_type" class="link_type" value="1">
						Link to external URL
					</label>
					<input type="text" data-target='title' class="external_title" value="" placeholder="title">
					<input type="text" data-target='url' class="external_link" value="" placeholder="Link">
				</div>
				<div class="link_type">
					<label>
						<input type="radio" name="link_type" value="2" class="link_type">
						 Link to internal page 
					</label>
					<input type="text" data-target='title' class="internal_title" value="" placeholder="title">
					<input type="text" data-target='url' class="internal_link" value="" placeholder="Page num">
				</div>
			</div>
			<div class="btn-group">
				<button class="editable-btn delete">
					<i class="far fa-trash-alt"></i>
				</button>
			</div>
		<?php } ?>
	</div>
</div>


<div class="whiteout-editable-menu element_editor" data-element-id="false">
	<div class="btn-group-wrap editor_content">
		<div class="btn-group">
			<button class="editable-btn" title="Border">
				<i class="fa fa-minus"></i>
			</button>
			<ul class="tools-dropdown-menu border-selector">
				<li class="divider"></li>
				@foreach($default_borders as $border)
					<li>
						<a class="set-border" style="display: block; height: {{$border}}px; background-color: black; " href="#">
						</a>
					</li>
				@endforeach
			</ul>
		</div>

		<div class="btn-group change_border_color">
			<button class="editable-btn" title="Border color">
				<i class="far fa-square"></i>
			</button>
			@include ('inc.colorpicker')
		</div>

		<div class="btn-group change_background_color">
			<button class="editable-btn" title="Bg color">
				<i class="fas fa-square-full"></i>
			</button>
			@include ('inc.colorpicker')
		</div>
		
		<div class="btn-group">
			<button class="editable-btn delete_whiteout">
				<i class="far fa-trash-alt"></i>
			</button>
		</div>
	</div>
</div>

<div class="forms-editable-menu element_editor ff-editable-menu" data-element-id="false">
	<a href="#" class="close_editor close" onclick="jQuery('.forms-editable-menu').hide(); return false;"><span>×</span></a>
	<div class="field_row field_name_row form-group">
		<label>
			Field name
			<input type="text" id="field_name">
		</label>
	</div>
	<div class="field_row  field_value_row form-group">
		<label>
			Field value
			<input type="text" id="field_value">
		</label>
	</div>
	<div class="field_row  show_if_select field_options_row form-group field-options-group">
		<label>
			 Options (one per line)
			<textarea id="field_options"></textarea>
		</label>
	</div>
	<div class="field_row  show_if_select field_allow_multiple_row form-group checkbox">
		<label>
			<input type="checkbox" id="field_allow_multiple">
			 Allow multiple selections 
		</label>
	</div>
	
	<div class="form-group bottom-btns">
		<div class="btn-group">
			<button class="button editable-btn clone_element">
				<i class="far fa-clone"></i>
			</button>
			<button class="button editable-btn delete_form_element">
				<i class="far fa-trash-alt"></i>
			</button>
		</div>
	</div>
</div>


<div class="annotate-editable-menu element_editor ff-editable-menu" data-element-id="false">
	<a href="#" class="close_editor close" onclick="jQuery('.annotate-editable-menu').hide(); return false;"><span>×</span></a>
	<div class="field_row field_name_row form-group">
		<label>
			Title
			<input type="text" id="annotate_title">
		</label>
	</div>
	<div class="field_row  field_options_row form-group field-options-group">
		<label>
			 Text
			<textarea id="annotate_content"></textarea>
		</label>
	</div>
	
	<div class="form-group bottom-btns">
		<div class="btn-group">
			<button class="button editable-btn delete_annotate">
				<i class="far fa-trash-alt"></i>
			</button>
		</div>
	</div>
</div>



