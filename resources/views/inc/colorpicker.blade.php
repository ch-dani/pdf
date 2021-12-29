<div class="tools-dropdown-menu">
	<ul class="color-opts">
		@if(isset($default_colors))
		@foreach($default_colors as $color)
			<li><a style="background-color: {{$color}}" class="color-swatch" href="#"></a></li>
		@endforeach
		@endif
		<li><a style="background-color: #FFFFFF; border-color: #CCC;" class="color-swatch" href="#"></a></li>
		<li>
			<a style="background-color: transparent; border-color: #CCC; position: relative;" class="color-swatch" href="#">
<img src="/img/trans-icon.png">
			</a>
		</li>
	</ul>
</div>
