<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link  rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                   aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i> ~
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                 aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                               aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="<?php echo e(route('admin')); ?>" target="_blank" data-toggle="tooltip"
               data-placement="bottom" title="home" role="button">
                <i class="fas fa-home fa-fw"></i>
            </a>
        </li>

        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1" id="messageT" data-url="<?php echo e(route('messages.five')); ?>">
            <?php echo $__env->make('message::message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo e(Auth()->user()->name); ?></span>
                <?php $user = Auth()->user(); ?>
                <?php if($user && $user->getFirstMediaUrl('photo')): ?>
                    <img class="img-profile rounded-circle" src="<?php echo e($user->getFirstMediaUrl('photo')); ?>">
                <?php else: ?>
                    <img class="img-profile rounded-circle" src="<?php echo e(asset('backend/img/avatar.png')); ?>">
                <?php endif; ?>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="<?php echo e(route('user-profile')); ?>">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="<?php echo e(route('front.index')); ?>">
                    <i class="fas fa-shopify fa-sm fa-fw mr-2 text-gray-400"></i>
                    Web shop
                </a>
                <?php if(auth()->guard()->check()): ?>
                    <?php if(session('impersonated_by')): ?>
                        <a class="dropdown-item" href="<?php echo e(route('users.leave-impersonate')); ?>">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Leave
                            Impersonation
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"
                   onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> <?php echo e(__('Logout')); ?>

                </a>

                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="hidden-form">
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        </li>

    </ul>
    <div class="dropdown">
        <button type="button" class="btn header-item waves-effect" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <?php switch(Session::get('locale', 'en')):
                case ('mk'): ?>
                    <img src="<?php echo e(asset('images/north-macedonia.png')); ?>" alt="Macedonian Language" height="32">
                    <?php break; ?>
                <?php case ('de'): ?>
                    <img src="<?php echo e(asset('images/germany.png')); ?>" alt="German Language" height="32">
                    <?php break; ?>
                <?php default: ?>
                    <img src="<?php echo e(asset('images/united-kingdom.png')); ?>" alt="English Language" height="32">
            <?php endswitch; ?>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?php echo e(route('language.switch', 'en')); ?>">English</a>
            <a class="dropdown-item" href="<?php echo e(route('language.switch', 'mk')); ?>">Macedonian</a>
            <a class="dropdown-item" href="<?php echo e(route('language.switch', 'de')); ?>">German</a>
        </div>
    </div>
</nav>
<?php /**PATH /var/www/Modules/Admin/Resources/views/layouts/header.blade.php ENDPATH**/ ?>