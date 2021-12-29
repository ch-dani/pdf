@extends('layouts.layout')

@section('content')
    <section class="main-banner">
        <div class="container">

            <div class="app-title" style="width: 100%;">
                <div class="wrapper">
                    <h1>{{ $page->title }}</h1>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="post">
                {!! $page->content !!}
            </div>
        </div>
    </section>
@endsection
