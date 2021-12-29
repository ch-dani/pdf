<div class="tools-dropdown-menu">
	<ul class="color-opts">
		@foreach($default_colors as $color)
			<li><a style="background-color: {{$color}}" class="color-swatch" href="#"></a></li>
		@endforeach
		<li><a style="background-color: #FFFFFF; border-color: #CCC;" class="color-swatch" href="#"></a></li>
		<li>
			<a style="background-color: transparent; border-color: #CCC; position: relative;" class="color-swatch" href="#">
				<div class="diagonal-red-line"></div>
			</a>
		</li>
	</ul>
</div>
