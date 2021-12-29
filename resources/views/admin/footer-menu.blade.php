@extends('layouts.admin-menu')

@section('content')
    <script>
        var FooterMenuSort = {};
        var BottomMenuSort = {};

        @foreach ($MenuFooter as $menu)
            FooterMenuSort[{{ $menu->id }}] = {{ $menu->sort }};
        @endforeach

        @foreach ($MenuBottom as $menu)
            BottomMenuSort[{{ $menu->id }}] = {{ $menu->sort }};
        @endforeach
    </script>
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Footer Menu
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header">
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
                                <h3 class="box-title pull-left">Footer Menu</h3>
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
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
@endsection
