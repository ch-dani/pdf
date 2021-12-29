@extends('layouts.admin-menu')

@section('content')
    <script>
        var MainMenuSort = {};
        var ToolsMenuSort = {};
        

        @foreach ($Menu[0] as $menu)
            MainMenuSort[{{ $menu->id }}] = {{ $menu->sort }};
        @endforeach

        @foreach ($MenuCategories as $category_id => $category)
            ToolsMenuSort[{{ $category_id }}] = {
                sort: {{ $category->sort }},
                items: {}
            };
            @if (isset($Menu[$category_id]))
                @foreach ($Menu[$category_id] as $menu)
                    ToolsMenuSort[{{ $category_id }}]['items'][{{ $menu->id }}] = {{ $menu->sort }};
                @endforeach
            @endif
        @endforeach
    </script>
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Menu
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header">
                                <ul class="nav nav-tabs">
                                    <li class="dropdown">
                                        <a class="dropdown-toggle toggle_form_selected" data-toggle="dropdown" href="#" aria-expanded="false">
                                            Item menu <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li role="presentation"><a role="menuitem" tabindex="-1" class="toggle_form" href="#">Category</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="box-body">
                                <form id="frmEdit" class="form-horizontal">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" id="menu_id">
                                    <input type="hidden" name="type" value="item" id="menu_type">

                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            @foreach ($Languages as $key => $Language)
                                                <li {!! $key == 0 ? 'class="active"' : '' !!}><a href="#{{ str_replace(' ', '_', $Language->name) }}" data-toggle="tab"><img style=" margin-right: 10px; " src="{{ $Language->flag }}" />{{ $Language->name }}</a></li>
                                            @endforeach
                                        </ul>
                                        <div class="tab-content">
                                            @foreach ($Languages as $key => $Language)
                                                <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ str_replace(' ', '_', $Language->name) }}">
                                                    <div class="form-group">
                                                        <label for="menu_title[{{ $Language->id }}]" class="col-sm-2 control-label">Title</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control lang_title" data-id="{{ $Language->id }}" id="menu_title[{{ $Language->id }}]" name="title[{{ $Language->id }}]" placeholder="Text">
                                                        </div>
                                                    </div>
                                                    <div class="form-group hide_category">
                                                        <label for="menu_tooltip[{{ $Language->id }}]" class="col-sm-2 control-label">Tooltip</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control lang_tooltip" data-id="{{ $Language->id }}" id="menu_tooltip[{{ $Language->id }}]" name="tooltip[{{ $Language->id }}]" placeholder="Text">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <!-- /.tab-pane -->
                                        </div>
                                        <!-- /.tab-content -->
                                    </div>
                                    <!-- nav-tabs-custom -->

                                    <div class="form-group hide_category">
                                        <label for="menu_url" class="col-sm-2 control-label">URL</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="menu_url" name="url" placeholder="URL">
                                        </div>
                                    </div>
                                    <div class="form-group hide_category">
                                        <label for="menu_target" class="col-sm-2 control-label">Target</label>
                                        <div class="col-sm-10">
                                            <select id="menu_target" name="target" class="form-control">
                                                <option value="_self">Self</option>
                                                <option value="_blank">Blank</option>
                                                <option value="_top">Top</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group hide_category">
                                        <label for="menu_new" class="col-sm-2 control-label">New</label>
                                        <div class="col-sm-10">
                                            <input type="checkbox" name="new" value="1" id="menu_new">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="box-footer">
                                <button type="button" id="UpdateMenu" class="btn btn-primary" disabled>
                                    <i class="fa fa-refresh"></i> Update
                                </button>
                                <button type="button" id="AddMenu" class="btn btn-success">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="MenuItemsBox">
                        <div class="box box-primary">
                            <div class="box-header clearfix">
                                <h3 class="box-title pull-left">Main Menu</h3>
                                <div class="pull-right">
                                    <button id="ResetDefault" type="button" class="btn btn-primary">
                                        <i class="glyphicon glyphicon-ok"></i> Restore defaults
                                    </button>
                                    <button id="CancelChanges" type="button" class="btn btn-danger" disabled>
                                        <i class="glyphicon glyphicon-ok"></i> Cancel changes
                                    </button>
                                    <button id="SaveMenu" type="button" class="btn btn-success" disabled>
                                        <i class="glyphicon glyphicon-ok"></i> Save
                                    </button>
                                </div>
                            </div>
                            <div class="box-body" id="cont">
                                <ul id="myList" class="sortableLists list-group">
                                    @if (isset($Menu[0]))
                                        @foreach ($Menu[0] as $menu)
                                            @php
                                                $titles = json_decode($menu->title, true);
                                                $tooltips = json_decode($menu->tooltip, true);
                                            @endphp
                                            <li class="list-group-item"
                                                @foreach ($Languages as $Language)
                                                    data-title-{{ $Language->id }}="{{ (isset($titles[$Language->id]) and !empty($titles[$Language->id])) ? $titles[$Language->id] : '' }}"
                                                    data-tooltip-{{ $Language->id }}="{{ (isset($tooltips[$Language->id]) and !empty($tooltips[$Language->id])) ? $tooltips[$Language->id] : '' }}"
                                                @endforeach
                                                data-url="{{ $menu->url }}"
                                                data-target="{{ $menu->target }}"
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
                                    <h3 class="box-title pull-left">Convert Menu</h3>
                                </div>

                                <ul id="ConvertTools" class="sortableLists list-group">
                                	
                                	@foreach($menuConv as $menu)
		                                @php
		                                    $titles = json_decode($menu->title, true);
		                                    $tooltips = json_decode($menu->tooltip, true);
		                                @endphp

		                                <li class="list-group-item"
		                                    @foreach ($Languages as $Language)
		                                        data-title-{{ $Language->id }}="{{ (isset($titles[$Language->id]) and !empty($titles[$Language->id])) ? $titles[$Language->id] : '' }}"
		                                        data-tooltip-{{ $Language->id }}="{{ (isset($tooltips[$Language->id]) and !empty($tooltips[$Language->id])) ? $tooltips[$Language->id] : '' }}"
		                                    @endforeach
		                                    data-url="{{ $menu->url }}"
		                                    data-target="{{ $menu->target }}"
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
                                                            $tooltips = json_decode($menu->tooltip, true);
                                                        @endphp
                                                        <li class="list-group-item"
                                                            @foreach ($Languages as $Language)
                                                                data-title-{{ $Language->id }}="{{ (isset($titles[$Language->id]) and !empty($titles[$Language->id])) ? $titles[$Language->id] : '' }}"
                                                                data-tooltip-{{ $Language->id }}="{{ (isset($tooltips[$Language->id]) and !empty($tooltips[$Language->id])) ? $tooltips[$Language->id] : '' }}"
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
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
@endsection
