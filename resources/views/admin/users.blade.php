@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Users
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Users</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All Users</h3>
                                &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                                <label>
                                	<input <?= $only_subscribed?"checked":"" ?> id="show_only_sub"  type="checkbox" > Show only subscribed
                                </label>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="Users" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;">#</th>
                                            <th>User Name</th>
                                            <th>User Email</th>
                                            <th>Country</th>
                                            <th>User Status</th>
                                            <th>User Activity</th>
                                            <th>Subscribed</th>
                                            <th style="width: 130px;">Registered</th>
                                            <th style="width: 70px;">Documents</th>
                                            <th style="width: 280px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach ($Users as $User)
                                                <tr>
                                                    <td>{{ $User->id }}</td>
                                                    <td>{{ $User->name }}</td>
                                                    <td>{{ $User->email }}</td>
                                                    <td>{{ $User->payment_country['name'] ?? '' }}</td>
                                                    <td>{{ ucfirst($User->status) }}</td>
                                                    <td>
                                                        @if (strtotime($User->last_activity) > time() - 180)
                                                            <span class="label bg-green" title="{{ $User->last_activity }}">Online</span>
                                                        @else
                                                            <span class="label bg-gray" title="{{ $User->last_activity }}">Offline</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                    	@if($User->subscription)
                                                    		<?php 
		                                                		$userSubscription = $User->subscription;
		                                                		$plan = $userSubscription->subscriptionPlan;
		                                                		echo $plan->name."<br>";
                                                    		?>
                                                    		
                                                    		@if($userSubscription->next_payment_at)
                                                    		Next payment
                                                    		{{$userSubscription->next_payment_at->format('m/d/Y')}}
                                                    		@endif
                                                    	@endif
                                                    </td>
                                                    
                                                    <td>{{ date('F d, Y', strtotime($User->created_at)) }}</td>
                                                    <td>{{ $User->documents_count }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('admin-edit-user', ['id' => $User->id]) }}" class="btn btn-info">Edit</a>
                                                            <a href="{{ route('admin-show-user', ['id' => $User->id]) }}" class="btn btn-success">View</a>
                                                            <a href="#" class="btn btn-danger DeleteUser" data-id="{{ $User->id }}">Delete</a>
                                                            <a href="{{ route('admin-login-user', ['id' => $User->id]) }}" class="btn btn-info">Login as a User</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>User Name</th>
                                            <th>User Email</th>
                                            <th>Country</th>
                                            <th>User Status</th>
                                            <th>User Activity</th>
                                            <th>Subscribed</th>
                                            <th>Registered</th>
                                            <th>Documents</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </section>

        </div>
    </div>
    <style>
    	.btn-group{
    		display: flex;
    	}
    	.btn-group a{
    		margin-left: 5px !important;
    	}
    </style>
    <script>
		document.addEventListener("DOMContentLoaded", function(event) {
			jQuery(document).on("change", "#show_only_sub", function(){
				var url = ("<?= url()->current(); ?>");
				if($(this).is(":checked")){
					window.location = url+"?only_subscribed=1";
				}else{
					window.location = url;
				}
			});
    	});
    </script>
    
    
@endsection
