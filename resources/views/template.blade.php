
@extends('layouts.layout')

@section('content-freeconvert')

<main>



    <div class="wrapper_single">
        <div class="container">
            <div class="row">
                <div class="col">


                    <div class="wrapper_content">
                    	                    <h1>{{ $page->title }}</h1>

						<div class="container">
							<div class="post">
								{!! $page->content !!}
							</div>
						</div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

	
</main>
@endsection
