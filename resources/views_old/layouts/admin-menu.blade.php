<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DeftPDF | Dashboard</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('admin-ui/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin-ui/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('admin-ui/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{ asset('admin-ui/bower_components/jvectormap/jquery-jvectormap.css') }}">
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
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>A</b>LT</span>
            <!-- logo for regular state and mobile devices -->
            <svg width="93" height="32" viewBox="0 0 93 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="32" height="32" rx="3" fill="url(#paint0_linear)"/>
                <rect width="32" height="32" rx="3" fill="url(#paint1_linear)"/>
                <rect width="32" height="32" rx="3" fill="url(#paint2_linear)"/>
                <path id="logo-icon"
                      d="M10 10H15.4205C16.7159 10 17.858 10.2514 18.8466 10.7543C19.8466 11.2457 20.6193 11.9429 21.1648 12.8457C21.7216 13.7486 22 14.8 22 16C22 17.2 21.7216 18.2514 21.1648 19.1543C20.6193 20.0571 19.8466 20.76 18.8466 21.2629C17.858 21.7543 16.7159 22 15.4205 22H10V10ZM15.2841 19.72C16.4773 19.72 17.4261 19.3886 18.1307 18.7257C18.8466 18.0514 19.2045 17.1429 19.2045 16C19.2045 14.8571 18.8466 13.9543 18.1307 13.2914C17.4261 12.6171 16.4773 12.28 15.2841 12.28H12.7614V19.72H15.2841Z"
                      fill="white"/>
                <path id="logo-text"
                      d="M39.996 11.6H43.812C44.724 11.6 45.528 11.776 46.224 12.128C46.928 12.472 47.472 12.96 47.856 13.592C48.248 14.224 48.444 14.96 48.444 15.8C48.444 16.64 48.248 17.376 47.856 18.008C47.472 18.64 46.928 19.132 46.224 19.484C45.528 19.828 44.724 20 43.812 20H39.996V11.6ZM43.716 18.404C44.556 18.404 45.224 18.172 45.72 17.708C46.224 17.236 46.476 16.6 46.476 15.8C46.476 15 46.224 14.368 45.72 13.904C45.224 13.432 44.556 13.196 43.716 13.196H41.94V18.404H43.716ZM56.1021 16.796C56.1021 16.82 56.0901 16.988 56.0661 17.3H51.1821C51.2701 17.7 51.4781 18.016 51.8061 18.248C52.1341 18.48 52.5421 18.596 53.0301 18.596C53.3661 18.596 53.6621 18.548 53.9181 18.452C54.1821 18.348 54.4261 18.188 54.6501 17.972L55.6461 19.052C55.0381 19.748 54.1501 20.096 52.9821 20.096C52.2541 20.096 51.6101 19.956 51.0501 19.676C50.4901 19.388 50.0581 18.992 49.7541 18.488C49.4501 17.984 49.2981 17.412 49.2981 16.772C49.2981 16.14 49.4461 15.572 49.7421 15.068C50.0461 14.556 50.4581 14.16 50.9781 13.88C51.5061 13.592 52.0941 13.448 52.7421 13.448C53.3741 13.448 53.9461 13.584 54.4581 13.856C54.9701 14.128 55.3701 14.52 55.6581 15.032C55.9541 15.536 56.1021 16.124 56.1021 16.796ZM52.7541 14.864C52.3301 14.864 51.9741 14.984 51.6861 15.224C51.3981 15.464 51.2221 15.792 51.1581 16.208H54.3381C54.2741 15.8 54.0981 15.476 53.8101 15.236C53.5221 14.988 53.1701 14.864 52.7541 14.864ZM59.4004 13.688H61.0564V15.128H59.4484V20H57.5764V15.128H56.5804V13.688H57.5764V13.4C57.5764 12.664 57.7924 12.08 58.2244 11.648C58.6644 11.216 59.2804 11 60.0724 11C60.3524 11 60.6164 11.032 60.8644 11.096C61.1204 11.152 61.3324 11.236 61.5004 11.348L61.0084 12.704C60.7924 12.552 60.5404 12.476 60.2524 12.476C59.6844 12.476 59.4004 12.788 59.4004 13.412V13.688ZM66.153 19.688C65.969 19.824 65.741 19.928 65.469 20C65.205 20.064 64.925 20.096 64.629 20.096C63.861 20.096 63.265 19.9 62.841 19.508C62.425 19.116 62.217 18.54 62.217 17.78V15.128H61.221V13.688H62.217V12.116H64.089V13.688H65.697V15.128H64.089V17.756C64.089 18.028 64.157 18.24 64.293 18.392C64.437 18.536 64.637 18.608 64.893 18.608C65.189 18.608 65.441 18.528 65.649 18.368L66.153 19.688ZM70.9718 11.6C71.7158 11.6 72.3598 11.724 72.9038 11.972C73.4558 12.22 73.8798 12.572 74.1758 13.028C74.4718 13.484 74.6198 14.024 74.6198 14.648C74.6198 15.264 74.4718 15.804 74.1758 16.268C73.8798 16.724 73.4558 17.076 72.9038 17.324C72.3598 17.564 71.7158 17.684 70.9718 17.684H69.2798V20H67.3358V11.6H70.9718ZM70.8638 16.1C71.4478 16.1 71.8918 15.976 72.1958 15.728C72.4998 15.472 72.6518 15.112 72.6518 14.648C72.6518 14.176 72.4998 13.816 72.1958 13.568C71.8918 13.312 71.4478 13.184 70.8638 13.184H69.2798V16.1H70.8638ZM76.0077 11.6H79.8237C80.7357 11.6 81.5397 11.776 82.2357 12.128C82.9397 12.472 83.4837 12.96 83.8677 13.592C84.2597 14.224 84.4557 14.96 84.4557 15.8C84.4557 16.64 84.2597 17.376 83.8677 18.008C83.4837 18.64 82.9397 19.132 82.2357 19.484C81.5397 19.828 80.7357 20 79.8237 20H76.0077V11.6ZM79.7277 18.404C80.5677 18.404 81.2357 18.172 81.7317 17.708C82.2357 17.236 82.4877 16.6 82.4877 15.8C82.4877 15 82.2357 14.368 81.7317 13.904C81.2357 13.432 80.5677 13.196 79.7277 13.196H77.9517V18.404H79.7277ZM87.8658 13.16V15.38H91.7538V16.94H87.8658V20H85.9218V11.6H92.2698V13.16H87.8658Z"
                      fill="#ffffff"/>
                <defs>
                    <linearGradient id="paint0_linear" x1="37.8325" y1="-10.88" x2="-2.75654" y2="-8.08909"
                                    gradientUnits="userSpaceOnUse">
                        <stop stop-color="#61DFAA"/>
                        <stop offset="0.793217" stop-color="#2EB9B7"/>
                    </linearGradient>
                    <linearGradient id="paint1_linear" x1="0" y1="32" x2="36.5251" y2="25.5246"
                                    gradientUnits="userSpaceOnUse">
                        <stop stop-color="#ED6E76"/>
                        <stop offset="1" stop-color="#F2AA6F"/>
                    </linearGradient>
                    <linearGradient id="paint2_linear" x1="0" y1="32" x2="36.5251" y2="25.5246"
                                    gradientUnits="userSpaceOnUse">
                        <stop stop-color="#4298E8"/>
                        <stop offset="1" stop-color="#8044DB"/>
                    </linearGradient>
                </defs>
            </svg>
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
                            <img src="{{ asset(\Auth::user()->avatar) }}" class="user-image" alt="User Image">
                            <span class="hidden-xs">{{ \Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="{{ asset(\Auth::user()->avatar) }}" class="img-circle" alt="User Image">

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

    <footer>

    </footer>


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
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
	<!-- <script src="{{ asset('admin-ui/dist/js/pages/dashboard.js') }}"></script> -->

    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('admin-ui/dist/js/demo.js') }}"></script>

    <script src='{{ asset('admin-ui/bower_components/bs-iconpicker/jquery-menu-editor.js') }}'></script>
    <script src='{{ asset('admin-ui/bower_components/bs-iconpicker/iconset-fontawesome-4.2.0.min.js') }}'></script>
    <script src='{{ asset('admin-ui/bower_components/bs-iconpicker/bootstrap-iconpicker.min.js') }}'></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.32.2/dist/sweetalert2.all.min.js"></script>

    @if (isset($js))
        @foreach ($js as $src)
            <script src="{{ $src }}"></script>
        @endforeach
    @endif

	</body>
</html>