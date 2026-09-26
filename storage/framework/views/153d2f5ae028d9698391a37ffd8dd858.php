<!DOCTYPE html>
<html lang="en">

<?php echo $__env->make('admin::layouts.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <?php echo $__env->make('admin::layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
            <?php echo $__env->make('admin::layouts.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- End of Topbar -->
            <?php echo $__env->make('core::notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!-- Begin Page Content -->
            <?php echo $__env->yieldContent('content'); ?>

            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->
<?php echo $__env->make('admin::layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>

</html>
<?php /**PATH /var/www/Modules/Admin/Resources/views/layouts/master.blade.php ENDPATH**/ ?>