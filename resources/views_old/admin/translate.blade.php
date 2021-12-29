@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Translate Pricing
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li class="active">Translate pricing</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="transPriceForm">
                            {{ csrf_field() }}
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Settings</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="aws_pub">AWS Access key ID</label>
                                                <input value="{{ \App\Option::option('aws_pub') }}"
                                                       type="text" name="aws_pub"
                                                       id="aws_pub" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="aws_priv">AWS Secret access key</label>
                                                <input value="{{ \App\Option::option('aws_priv') }}"
                                                       type="text" name="aws_priv"
                                                       id="aws_priv" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="stripe_pub">Stripe pub key</label>
                                                <input value="{{ \App\Option::option('stripe_pub') }}"
                                                       type="text" name="stripe_pub"
                                                       id="stripe_pub" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label for="stripe_priv">Stripe priv key</label>
                                                <input value="{{ \App\Option::option('stripe_priv') }}"
                                                       type="text" name="stripe_priv"
                                                       id="stripe_priv" class="form-control">
                                            </div>




                                            <div class="form-group">
                                                <label for="free_translate_count">Free chars count</label>
                                                <input value="{{ \App\Option::option('free_translate_count') }}"
                                                       type="text" name="free_translate_count"
                                                       id="free_translate_count" class="form-control">
                                            </div>



                                            <div class="form-group">
                                                <label for="translate_count">Paid chars count</label>
                                                <input value="{{ \App\Option::option('translate_count') }}"
                                                       type="text" name="translate_count"
                                                       id="translate_count" class="form-control">
                                            </div>


                                            <div class="form-group">
                                                <label for="translate_price">Translate price</label>
                                                <input value="{{ \App\Option::option('translate_price') }}"
                                                       type="text" name="translate_price"
                                                       id="translate_price" class="form-control">
                                            </div>
                                            


                                        </div>
                                    </div>




                                    <div class="row">
                                        <div class="col-md-12">
                                        	@if(false)
		                                    	<label>Prices ranges</label>
		                                    	<table id="translate_price_table">
		                                    		<thead>
				                                		<tr>
				                                			<th>Chars more then</th>
				                                			<th>Price</th>
				                                			<th>Action</th>
				                                		</tr>
		                                    		</thead>
		                                    		<tbody>
				                                		<tr class="example_tr hidden">
				                                			<td>
								                            	<input value="0"  type="text" name="trans_prices[%num%][range]" class="form-control">	                                        			
				                                			</td>                                        		
				                                			<td>
								                            	<input value="0"  type="text" name="trans_prices[%num%][price]" class="form-control">	                                        			
				                                			</td>                                        		
				                                			<td>
				                                				<button type="button" class="remove_range btn btn-primary"><i class="fa fa-trash" aria-hidden="true"></i></button>
				                                			</td>                                        		
			                                			</tr>

		                                    			@foreach($ranges as $k=>$r)
					                                		<tr class="">
					                                			<td>
									                            	<input value="{{ $r['chars'] }}"  type="text" name="trans_prices[{{$k}}][range]" class="form-control">	                                        			
					                                			</td>                                        		
					                                			<td>
									                            	<input value="{{ $r['price'] }}"  type="text" name="trans_prices[{{$k}}][price]" class="form-control">	                                        			
					                                			</td>                                        		
					                                			<td>
					                                				<button type="button" class="remove_range btn btn-primary"><i class="fa fa-trash" aria-hidden="true"></i></button>
					                                			</td>                                        		
				                                			</tr>
		                                    			@endforeach




		                                    		</tbody>
		                                    		
		                                    		<tfoot>
		                                    			<tr>
		                                    				<td colspan=3 class="text-right">
		                                    					<button type="button" id="add_new_range" class="btn btn-primary">Add new range</button>
		                                    				</td>
		                                    			</tr>
		                                    		</tfoot>
		                                    		
		                                    		
		                                    	</table>
		                                    @endif
                                            <div class="form-group">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </form>
                    </div>
                </div>
            </section>

        </div>
    </div>
    
    <style>
		#translate_price_table{
			width: 100%;
		}
		#translate_price_table td{
			padding: 10px;
			border: 1px solid #ecf0f5;
    	}
    	
    	#translate_price_table td:last-child{
    		text-align: center;
    	}
    	#translate_price_table td.text-right{
    		text-align: right;
    	}
    	
    	
    </style>
    
@endsection
