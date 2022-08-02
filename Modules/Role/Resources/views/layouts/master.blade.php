<!DOCTYPE html>
<html lang="en">

@include('admin::layouts.head')
<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    @include('admin::layouts.sidebar')
    <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            @include('admin::layouts.header')
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            @yield('content')

            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->
@include('admin::layouts.footer')

</body>

</html>
