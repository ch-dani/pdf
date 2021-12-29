@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Dashboard
                </h1>
            </section>
            <section class="content">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $ActiveUsers }}</h3>

                                <p>Active Users Today</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>{{ $PDFToday }}</h3>

                                <p>PDFs Proceeded Today</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>{{ $UserToday }}</h3>

                                <p>User Registrations Today</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>{{ $UniqueVisitors }}</h3>

                                <p>Unique Visitors Today</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->
                <!-- Main row -->
                <div class="row">
                    <!-- Left col -->
                    <section class="col-lg-7 connectedSortable">
                        <!-- Custom tabs (Charts with tabs)-->

                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Last 20 documents edited</h3>
                            </div>
                            <div class="box-body">
                                <table id="Documents" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width: 30px;">#</th>
                                        <th>User</th>
                                        <th style="width: 40px;">Date</th>
                                        <th>Original File</th>
                                        <th>Edited File</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($Documents as $Document)
                                    
                                    
                                        <tr data-id="{{ $Document->id }}">
                                            <td>{{ $Document->id }}</td>
                                            <td>{!! $Document->role == 'admin' ? 'Administrator' : ( is_null($Document->user_id) ? 'Not authorized' : '<a href="'.route('admin-show-user', ['id' => $Document->user_id]).'">'.$Document->email.'</a>' ) !!}</td>
                                            <td>{{ date('M d', strtotime($Document->created_at)) }}</td>
                                            <td>
                                                <a href="{{ asset($Document->original_document) }}"
                                                   target="_blank">{{ $Document->original_name }}</a>
                                            </td>
                                            <td>
                                                @if (!is_null($Document->edited_document))
                                                	<?php if(strpos($Document->edited_document, "/var/www/freeconvert/public")!==false){ 
                                                		$url = str_replace("/var/www/freeconvert/public", "", $Document->edited_document);
                                                		?>
                                                		<a href="{{ asset($url) }}"
		                                                   target="_blank">edited_{{ $Document->original_name }}</a>   		
                                                	<?php }elseif(strpos($Document->edited_document, "/var/www/freeconvert/storage/app/uploads")!==false){ 
                                                		$url = str_replace("/var/www/freeconvert/storage/app/uploads", "/storage/uploads", $Document->edited_document);
                                                		?>
		                                                <a href="{{ asset($url) }}"
		                                                   target="_blank">edited_{{ $Document->original_name }}</a>
                                                	
                                                	<?php }else{ ?>
		                                                <a href="{{ asset($Document->edited_document) }}"
		                                                   target="_blank">edited_{{ $Document->original_name }}</a>
                                                    <?php } ?>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Date</th>
                                        <th>Original File</th>
                                        <th>Edited File</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                    </section>


                    <!-- /.Left col -->
                    <!-- right col (We are only adding the ID to make the widgets sortable)-->
                    <section class="col-lg-5 connectedSortable">

                        <!-- Map box -->
                        <div class="box box-solid bg-light-blue-gradient">
                            <div class="box-header">
                                <!-- tools box -->

                                <i class="fa fa-map-marker"></i>

                                <h3 class="box-title">
                                    Visitors
                                </h3>
                            </div>
                            <div class="box-body">
                                <div id="world-map" style="height: 250px; width: 100%;"></div>
                            </div>
                            <!-- /.box-body-->
                            <div class="box-footer no-border">
                                <div class="row">
                                    <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                        <div id="sparkline-1"></div>
                                        <div class="knob-label">Visitors</div>
                                    </div>
                                    <!-- ./col -->
                                    <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                        <div id="sparkline-2"></div>
                                        <div class="knob-label">Online</div>
                                    </div>
                                    <!-- ./col -->
                                    <div class="col-xs-4 text-center">
                                        <div id="sparkline-3"></div>
                                        <div class="knob-label">Exists</div>
                                    </div>
                                    <!-- ./col -->
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                        <!-- /.box -->

                    </section>
                    <!-- right col -->
                </div>
                <!-- /.row (main row) -->

            </section>
        </div>
    </div>

    <script>
        var visitorsData = {};
        @foreach ($MapVisitors as $MapVisitor)
            visitorsData['{{ $MapVisitor->iso_code }}'] = {{ $MapVisitor->total }};
        @endforeach
    </script>
@endsection
