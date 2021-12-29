@extends('layouts.admin')

@section('content')
    <div class="main-wrap">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<script>
			var page_id = {{ $page->id }};
			var save_url = "{{ route('ads_update', ['id'=>$page->id]) }}";
		</script>

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    {{ $page->title }} ads
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('ads') }}">Page ads</a>
                    </li>
                    <li class="active">{{ $page->title }} ads</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
								<div class="col-md-4">
									<div class="box box-success box-solid">
										<div class="box-body pad">
											<div class="form-group">
												<label>Status</label>
												<select class="form-control" name="show_ads">
													<option value="1">Show ads</option>
													<option value="0">Hide ads</option>
												</select>
											</div>
											<div class="form-group">
												<label>Published:</label>
												<div class="input-group date">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" value="{{ date('Y-m-d', strtotime($page->created_at)) }}" disabled class="form-control pull-right" id="datepicker">
												</div>
												<!-- /.input group -->
											</div>
											<div class="form-group">
												<label>Updated:</label>
												<div class="input-group date">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" value="{{ date('Y-m-d', strtotime($page->updated_at)) }}" disabled class="form-control pull-right" id="datepicker">
												</div>
												<!-- /.input group -->
											</div>
											<button type="button" class="btn btn-default btn-block" id="save_page_ads">Save</button>
										</div>
										<!-- /.box-body -->
									</div>
								</div>

									<div class="col-md-8">
										<div class="box box-success box-solid">
											<div class="box-body pad">
												<div class="form-group code_textarea_wrapper">
													<label for="exampleInputEmail1">Ads code</label>
													
													@foreach($ads as $ad)
														<div class="ad_outer">
															<textarea name="page_ads[]" class="code_textarea m-sm form-control" cols="50" rows="8" placeholder="Insert ads code...">{{ $ad->content }}</textarea>
															<div class="form-group">
																<button type="button" class="btn btn-danger btn_remove remove_existed_ad"  >Remove</button>
															</div>
														</div>
													@endforeach
													
												</div>
												<div class="form-group">
													<div class="row">
														<div class="col-md-3">
															<button type="button" class="btn btn-default btn-block add_new_code_textarea" id="add_new_ad">Add new</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>  



                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </section>

        </div>
    </div>
@endsection
