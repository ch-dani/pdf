<ul id="myList" class="sortableLists list-group">
    @if (isset($Menu[0]))
        @foreach ($Menu[0] as $menu)
            @php
                $titles = json_decode($menu->title, true);
            @endphp
            <li class="list-group-item"
                @foreach ($Languages as $Language)
                    data-title-{{ $Language->id }}="{{ (isset($titles[$Language->id]) and !empty($titles[$Language->id])) ? $titles[$Language->id] : '' }}"
                @endforeach
                data-url="{{ $menu->url }}"
                data-target="{{ $menu->target }}"
                data-tooltip="{{ $menu->tooltip }}"
                data-new="{{ $menu->new }}"
                data-menu-id="{{ $menu->id }}"
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
    <h3 class="box-title pull-left">Tools Menu</h3>
</div>
<ul id="AllTools" class="sortableLists list-group">
    @foreach ($MenuCategories as $category_id => $category)
        @php
            $titles = json_decode($category->title, true);
        @endphp
        <li class="list-group-item sortableListsOpen"
            @foreach ($Languages as $Language)
                data-title-{{ $Language->id }}="{{ (isset($titles[$Language->id]) and !empty($titles[$Language->id])) ? $titles[$Language->id] : '' }}"
            @endforeach
            data-category-id="{{ $category_id }}">
            <div style="margin-bottom: 10px;min-height: 20px;">
                <span class="txt">{{ isset($titles[1]) ? $titles[1] : '' }}</span>
                <div class="btn-group pull-right">
                    <a href="#" class="btn btn-default btn-xs btnEdit">Edit</a>
                    <a href="#" data-id="{{ $category_id }}" data-type="category" class="btn btn-danger btn-xs RemoveMenu">X</a>
                </div>
            </div>
            <ul>
                @if (isset($Menu[$category_id]))
                    @foreach ($Menu[$category_id] as $menu)
                        @php
                            $titles = json_decode($menu->title, true);
                        @endphp
                        <li class="list-group-item"
                            @foreach ($Languages as $Language)
                                data-title-{{ $Language->id }}="{{ (isset($titles[$Language->id]) and !empty($titles[$Language->id])) ? $titles[$Language->id] : '' }}"
                            @endforeach
                            data-url="{{ $menu->url }}"
                            data-target="{{ $menu->target }}"
                            data-tooltip="{{ $menu->tooltip }}"
                            data-new="{{ $menu->new }}"
                            data-menu-id="{{ $menu->id }}"
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
        </li>
    @endforeach
</ul>