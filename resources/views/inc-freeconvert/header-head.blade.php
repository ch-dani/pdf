<!doctype html>
<html lang="en">
<head>
<?php
$url='';
   if(Request::path()!='/'){
    $url=Request::path();
}
?>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="{{ isset($SeoGlobal['keywords']) ? $SeoGlobal['keywords'] : '' }}">
    <meta name="description" content="{{ isset($SeoGlobal['description']) ? $SeoGlobal['description'] : '' }}">
    <link rel="canonical" href="https://freeconvertpdf.com/{{$url}}"/>
    <title>{{ isset($SeoGlobal['title']) ? $SeoGlobal['title'] : '' }}</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/bootstrap-grid.css') }}">

    @yield('css')

    <link rel="stylesheet" href="{{ asset('freeconvert/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/main_roman.css') }}">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/main_oleg.css') }}">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/main_grisyuk.css') }}">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/main_alex.css') }}">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/media_main.css') }}">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/media_roman.css') }}">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/media_oleg.css') }}">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/main_anton.css') }}">
    <link rel="stylesheet" href="{{ asset('freeconvert/css/fixes.css') }}">
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-G3WNC97WGK"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-G3WNC97WGK');
</script>
</head>
<body <?php if (isset($body_class)) { ?>class="<?php echo $body_class; ?>" <?php } ?>>
