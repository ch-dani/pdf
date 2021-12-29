<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Freeconvert PDF | Dashboard</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('admin-ui/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin-ui/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('admin-ui/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{ asset('admin-ui/plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin-ui/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('admin-ui/dist/css/skins/_all-skins.min.css') }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- STYLESHEETS -->
    <link href="{{ asset('admin-ui/dist/css/StyleSheet.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/additional.css') }}" rel="stylesheet"/>

    <!-- DataTables with bootstrap 3 renderer -->
    <link rel="stylesheet" href="//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@7.32.2/dist/sweetalert2.min.css">

    @if (isset($css))
        @foreach ($css as $src)
            <link href="{{ $src }}" rel="stylesheet"/>
    @endforeach
@endif
<!-- STYLESHEETS END -->


</head>
<body class="skin-blue">
<header class="main-header">
    <!-- Logo -->
    <a href="{{ route('admin-dashboard') }}" class="logo">
        <span>Freeconvert PDF</span>
{{--        <span class="logo-mini"><b>A</b>LT</span>--}}
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
{{--                        <img src="{{ asset(\Auth::user()->avatar) }}" class="user-image"--}}
{{--                             alt="User Image">--}}
                        <span class="hidden-xs">{{ \Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
{{--                            <img src="{{ asset(\Auth::user()->avatar) }}" class="img-circle"--}}
{{--                                 alt="User Image">--}}
                            <p>
                                {{ \Auth::user()->name }}
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('admin-profile') }}" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ route('logout') }}" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

@yield('content')

<footer></footer>

<!-- jQuery 3 -->
<script src="{{ asset('admin-ui/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('admin-ui/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('admin-ui/bower_components/fastclick/lib/fastclick.js') }}"></script>
<!-- Morris.js charts -->
<script src="{{ asset('admin-ui/bower_components/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('admin-ui/bower_components/morris.js/morris.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('admin-ui/dist/js/adminlte.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('admin-ui/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap  -->
<script src="{{ asset('admin-ui/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('admin-ui/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- SlimScroll -->
<script src="{{ asset('admin-ui/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('admin-ui/bower_components/chart.js/Chart.js') }}"></script>

<!-- AdminLTE for demo purposes -->
<script src="{{ asset('admin-ui/dist/js/demo.js') }}"></script>

<!-- DataTables with bootstrap 3 renderer -->
<script src="//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.32.2/dist/sweetalert2.all.min.js"></script>

@if (isset($js))
    @foreach ($js as $src)
        <script src="{{ $src }}"></script>
    @endforeach
@endif

</body>
</html>
