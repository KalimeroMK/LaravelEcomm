<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin')}}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin</div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{route('admin')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    @can('super-admin')
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Banner
        </div>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
               aria-expanded="true"
               aria-controls="collapseTwo">
                <i class="fas fa-image"></i>
                <span>Banners</span>
            </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Banner Options:</h6>
                    <a class="collapse-item" href="{{route('banners.index')}}">Banners</a>
                    <a class="collapse-item" href="{{route('banners.create')}}">Add Banners</a>
                </div>
            </div>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider">
        <!-- Heading -->
    @endcan
    <div class="sidebar-heading">
        Shop
    </div>
    @hasrole('super-admin')

    <!-- Categories -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#categoryCollapse"
           aria-expanded="true" aria-controls="categoryCollapse">
            <i class="fas fa-sitemap"></i>
            <span>Category</span>
        </a>
        <div id="categoryCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Category Options:</h6>
                <a class="collapse-item" href="{{route('categories.index')}}">Category</a>
                <a class="collapse-item" href="{{route('categories.create')}}">Add Category</a>
            </div>
        </div>
    </li>
    {{-- Attrinute --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#attributeCollapse"
           aria-expanded="true" aria-controls="attributeCollapse">
            <i class="fas fa-cubes"></i>
            <span>Attribute</span>
        </a>
        <div id="attributeCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Attribute Options:</h6>
                <a class="collapse-item" href="{{route('attributes.index')}}">Attributes</a>
                <a class="collapse-item" href="{{route('attributes.create')}}">Add attribute</a>
            </div>
        </div>
    </li>
    {{-- Products --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#productCollapse"
           aria-expanded="true" aria-controls="productCollapse">
            <i class="fas fa-cubes"></i>
            <span>Products</span>
        </a>
        <div id="productCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Product Options:</h6>
                <a class="collapse-item" href="{{route('products.index')}}">Products</a>
                <a class="collapse-item" href="{{route('products.create')}}">Add Product</a>
            </div>
        </div>
    </li>
    {{-- Bandle --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bundleCollapse"
           aria-expanded="true" aria-controls="bundleCollapse">
            <i class="fas fa-cubes"></i>
            <span>Bundles</span>
        </a>
        <div id="bundleCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Bundle Options:</h6>
                <a class="collapse-item" href="{{route('bundles.index')}}">Bundles</a>
                <a class="collapse-item" href="{{route('bundles.create')}}">Add Bundle</a>
            </div>
        </div>
    </li>

    {{-- Brands --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#brandCollapse" aria-expanded="true"
           aria-controls="brandCollapse">
            <i class="fas fa-table"></i>
            <span>Brands</span>
        </a>
        <div id="brandCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Brand Options:</h6>
                <a class="collapse-item" href="{{route('brands.index')}}">Brands</a>
                <a class="collapse-item" href="{{route('brands.create')}}">Add Brand</a>
            </div>
        </div>
    </li>
    {{-- Shipping --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#shippingCollapse"
           aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-truck"></i>
            <span>Shipping</span>
        </a>
        <div id="shippingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Shipping Options:</h6>
                <a class="collapse-item" href="{{route('shippings.index')}}">Shipping</a>
                <a class="collapse-item" href="{{route('shippings.create')}}">Add Shipping</a>
            </div>
        </div>
    </li>
    {{-- Nwesletter --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#newsletterCollapse"
           aria-expanded="true" aria-controls="newsletterCollapse">
            <i class="fas fa-truck"></i>
            <span>Newsletters</span>
        </a>
        <div id="newsletterCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Newsletter Options:</h6>
                <a class="collapse-item" href="{{route('newsletters.index')}}">Newsletters</a>
                <a class="collapse-item" href="{{route('newsletters.create')}}">Add newsletter</a>
            </div>
        </div>
    </li>
    @endhasrole

    <!--Orders -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('orders.index')}}">
            <i class="fas fa-hammer fa-chart-area"></i>
            <span>Orders</span>
        </a>
    </li>

    <!-- Reviews -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('reviews.index')}}">
            <i class="fas fa-comments"></i>
            <span>Reviews</span></a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Posts
    </div>
    @hasrole('super-admin')

    <!-- Products -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#postCollapse" aria-expanded="true"
           aria-controls="postCollapse">
            <i class="fas fa-fw fa-folder"></i>
            <span>Posts</span>
        </a>
        <div id="postCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Post Options:</h6>
                <a class="collapse-item" href="{{route('posts.index')}}">Posts</a>
                <a class="collapse-item" href="{{route('posts.create')}}">Add Post</a>
            </div>
        </div>
    </li>

    <!-- Tags -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#tagCollapse" aria-expanded="true"
           aria-controls="tagCollapse">
            <i class="fas fa-tags fa-folder"></i>
            <span>Tags</span>
        </a>
        <div id="tagCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Tag Options:</h6>
                <a class="collapse-item" href="{{route('tags.index')}}">Tag</a>
                <a class="collapse-item" href="{{route('tags.create')}}">Add Tag</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('coupons.index')}}">
            <i class="fas fa-table"></i>
            <span>Coupon</span></a>
    </li>
    @endhasrole
    <!-- Comments -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('comments.index')}}">
            <i class="fas fa-comments fa-chart-area"></i>
            <span>Comments</span>
        </a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Heading -->
    <div class="sidebar-heading">
        General Settings
    </div>

    <!-- Users -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#userCollapse"
           aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-wrench"></i>
            <span>User</span>
        </a>
        <div id="userCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">User configuration:</h6>
                <a class="collapse-item" href="{{route('users.index')}}">User</a>
                @hasrole('super-admin')
                <a class="collapse-item" href="{{route('role.index')}}">Role</a>
                <a class="collapse-item" href="{{route('permissions.index')}}">Permission</a>
                @endhasrole
            </div>
        </div>
    </li>
    @hasrole('super-admin')
    <!-- General settings -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('settings.index')}}">
            <i class="fas fa-cog"></i>
            <span>Settings</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('all.notification')}}">
            <i class="fas fa-info"></i>
            <span>Notification</span></a>
    </li>
    {{-- Config --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#configCollapse"
           aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-wrench"></i>
            <span>Configuration</span>
        </a>
        <div id="configCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Configuration Options:</h6>
                <a class="collapse-item" href="{{route('activity')}}">Activity log</a>
                <a class="collapse-item" href="{{route('laravelblocker::blocker.index')}}">Blocked IP</a>
                <a class="collapse-item" href="{{ url('translations/') }}">Translation</a>
            </div>
        </div>
    </li>
    @endhasrole

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>


</ul>
