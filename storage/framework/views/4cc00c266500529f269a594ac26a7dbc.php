<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissable fade show">
        <button class="close" data-dismiss="alert" aria-label="Close">×</button>
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissable fade show">
        <button class="close" data-dismiss="alert" aria-label="Close">×</button>
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>
<?php if(Session::has('success_msg')): ?>
    <div class="alert alert-success">
        <strong><?php echo e(trans('messages.success')); ?>!</strong> <?php echo Session::get('success_msg'); ?>

    </div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="alert-primary"><?php echo e($error); ?></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?><?php /**PATH /var/www/Modules/Core/Resources/views/notification.blade.php ENDPATH**/ ?>