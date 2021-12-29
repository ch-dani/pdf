<ul id="myList" class="sortableLists list-group">
    @if (isset($MenuFooter))
        @foreach ($MenuFooter as $menu)
            @php
                $titles = json_decode($menu->title, true);
            @endphp
            <li class="list-group-item"
                @foreach ($Languages as $Language)
                    data-title-{{ $Language->id }}="{{ (isset($titles[$Language->id]) and !empty($titles[$Language->id])) ? $titles[$Language->id] : '' }}"
                @endforeach
                data-url="{{ $menu->url }}"
                data-target="{{ $menu->target }}"
                data-menu-id="{{ $menu->id }}"
                data-type="footer"
                data-none="1">
                <div>
                    <span class="txt">{{ isset($titles[1]) ? $titles[1] : '' }}</span>
                    <div class="btn-group pull-right">
                        <a href="#" class="btn btn-default btn-xs btnEdit">Edit</a>
                        <a href="#" data-id="{{ $menu->id }}" data-type="item" class="btn btn-danger btn-xs RemoveMenu">X</a>
                    </div>
                </div>
            </li>
        @endforeach
    @endif
</ul>
<div class="box-header clearfix" style="padding: 0px 0px 20px;">
    <h3 class="box-title pull-left">Bottom Menu</h3>
</div>
<ul id="myListBottom" class="sortableLists list-group">
    @if (isset($MenuBottom))
        @foreach ($MenuBottom as $menu)
            @php
                $titles = json_decode($menu->title, true);
            @endphp
            <li class="list-group-item"
                @foreach ($Languages as $Language)
                data-title-{{ $Language->id }}="{{ (isset($titles[$Language->id]) and !empty($titles[$Language->id])) ? $titles[$Language->id] : '' }}"
                @endforeach
                data-url="{{ $menu->url }}"
                data-target="{{ $menu->target }}"
                data-menu-id="{{ $menu->id }}"
                data-type="bottom"
                data-none="1">
                <div>
                    <span class="txt">{{ isset($titles[1]) ? $titles[1] : '' }}</span>
                    <div class="btn-group pull-right">
                        <a href="#" class="btn btn-default btn-xs btnEdit">Edit</a>
                        <a href="#" data-id="{{ $menu->id }}" data-type="item" class="btn btn-danger btn-xs RemoveMenu">X</a>
                    </div>
                </div>
            </li>
        @endforeach
    @endif
</ul>