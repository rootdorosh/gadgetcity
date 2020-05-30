<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>@yield('title')</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Tempusdominus Bbootstrap 4 -->
        <link rel="stylesheet" href="/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
        <!-- JQVMap -->
        <link rel="stylesheet" href="/adminlte/plugins/jqvmap/jqvmap.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="/adminlte/plugins/daterangepicker/daterangepicker.css">
        <!-- summernote -->
        <link rel="stylesheet" href="/adminlte/plugins/summernote/summernote-bs4.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <!-- Google Font: Source Sans Pro -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
        <link href="/backend/css/select2.css" rel="stylesheet">
        <link href="/backend/css/app.css" rel="stylesheet">
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">

            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="/adminlte/#"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="/adminlte/index3.html" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="/adminlte/#" class="nav-link">Contact</a>
                    </li>
                </ul>

            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="{{ r('admin.dashboard') }}" class="brand-link">
                    <span class="brand-text font-weight-light">GadgetCity</span>
                </a>

                <div class="sidebar">
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            @foreach (App\Base\CoreHelper::getMenu() as $item)
                            @permission($item['permission'])
                            <li class="nav-item
                                {{ !empty($item['children']) ? 'has-treeview' : '' }}
                                {{ !empty($item['module']) && request()->is('admin/' . $item['module'] . '*') ? 'menu-open' : '' }}
                                ">
                                <a href="{{ $item['route'] }}" class="nav-link">
                                    <i class="nav-icon fas {{ $item['icon'] }}"></i>
                                    <p>
                                        {{ $item['title'] }}
                                        <i class="fas {{ !empty($item['children']) ? 'fa-angle-left' : '' }} right"></i>
                                    </p>
                                </a>
                                @if (!empty($item['children']))
                                <ul class="nav nav-treeview">
                                    @foreach ($item['children'] as $child)
                                    @permission($child['permission'])
                                    <li class="nav-item">
                                        <a href="{{ $child['route'] }}"
                                           class="nav-link {{ (!empty($item['module']) && request()->is('admin/' . $item['module'] . '*')) && substr_count($child['route'], request()->path()) ? 'active' : '' }}"
                                        >
                                            <i class="nav-icon fas {{ $child['icon'] }} ml-2"></i>
                                            <p>{{ $child['title'] }}</p>
                                        </a>
                                    </li>
                                     @endpermission
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                            @endpermission
                            @endforeach
                        </ul>
                    </nav>
                </div>
            </aside>

            <div class="content-wrapper">

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid pt-3">
                        <div class="flash-message">
                            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                              @if(session($msg))
                              <p class="alert alert-{{ $msg }}">{{ session($msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                              @endif
                            @endforeach
                        </div>

                        @yield('content')
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
            <footer class="main-footer">
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block">
                </div>
            </footer>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
        </div>
        <!-- ./wrapper -->

        <!-- jQuery -->
        <script src="/adminlte/plugins/jquery/jquery.min.js"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="/adminlte/plugins/jquery-ui/jquery-ui.min.js"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
          $.widget.bridge('uibutton', $.ui.button)
        </script>
        <!-- Bootstrap 4 -->
        <script src="/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- ChartJS -->
        <script src="/adminlte/plugins/chart.js/Chart.min.js"></script>
        <!-- Sparkline -->
        <script src="/adminlte/plugins/sparklines/sparkline.js"></script>
        <!-- JQVMap -->
        <script src="/adminlte/plugins/jqvmap/jquery.vmap.min.js"></script>
        <script src="/adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
        <!-- jQuery Knob Chart -->
        <script src="/adminlte/plugins/jquery-knob/jquery.knob.min.js"></script>
        <!-- daterangepicker -->
        <script src="/adminlte/plugins/moment/moment.min.js"></script>
        <script src="/adminlte/plugins/daterangepicker/daterangepicker.js"></script>
        <!-- Tempusdominus Bootstrap 4 -->
        <script src="/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
        <!-- Summernote -->
        <script src="/adminlte/plugins/summernote/summernote-bs4.min.js"></script>
        <!-- overlayScrollbars -->
        <script src="/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
        <!-- AdminLTE App -->
        <script src="/adminlte/dist/js/adminlte.js"></script>


        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script>
            window._token = '{{ csrf_token()  }}';
        </script>
        <script src="/backend/js/app.js"></script>
        <script src="/backend/js/agrid.js"></script>

        @stack('scripts')

    </body>
</html>
