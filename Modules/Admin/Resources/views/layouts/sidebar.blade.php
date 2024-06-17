<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin')}}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">@lang('sidebar.admin')</div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{route('admin')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>@lang('sidebar.dashboard')</span></a>
    </li>
    @can('super-admin')
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            @lang('sidebar.banners')
        </div>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
               aria-expanded="true"
               aria-controls="collapseTwo">
                <i class="fas fa-image"></i>
                <span>@lang('sidebar.banners')</span>
            </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">@lang('sidebar.banner_options'):</h6>
                    <a class="collapse-item" href="{{route('banner.index')}}">@lang('sidebar.banners')</a>
                    <a class="collapse-item" href="{{route('banner.create')}}">@lang('sidebar.add_banners')</a>
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
    {{-- Attrinute --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#attributeCollapse"
           aria-expanded="true" aria-controls="attributeCollapse">
            <i class="fas fa-cubes"></i>
            <span>@lang('sidebar.attributes')</span>
        </a>
        <div id="attributeCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.attribute_options'):</h6>
                <a class="collapse-item" href="{{route('attributes.index')}}">@lang('sidebar.attributes')</a>
                <a class="collapse-item" href="{{route('attributes.create')}}">@lang('sidebar.add_attribute')</a>
            </div>
        </div>
    </li>
    {{-- Attrinute --}}
    {{-- Banner --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bannerCollapse" aria-expanded="true"
           aria-controls="brandCollapse">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.banners')</span>
        </a>
        <div id="bannerCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.brand_options'):</h6>
                <a class="collapse-item" href="{{route('banners.index')}}">@lang('sidebar.banners')</a>
                <a class="collapse-item" href="{{route('banners.create')}}">@lang('sidebar.add_banners')</a>
            </div>
        </div>
    </li>
    {{-- Brands --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#brandCollapse" aria-expanded="true"
           aria-controls="brandCollapse">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.brands')</span>
        </a>
        <div id="brandCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.brand_options'):</h6>
                <a class="collapse-item" href="{{route('brands.index')}}">@lang('sidebar.brands')</a>
                <a class="collapse-item" href="{{route('brands.create')}}">@lang('sidebar.add_brand')</a>
            </div>
        </div>
    </li>
    {{-- Bandle --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bundleCollapse"
           aria-expanded="true" aria-controls="bundleCollapse">
            <i class="fas fa-cubes"></i>
            <span>@lang('sidebar.bundles')</span>
        </a>
        <div id="bundleCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.bundles_options'):</h6>
                <a class="collapse-item" href="{{route('bundles.index')}}">@lang('sidebar.bundles')</a>
                <a class="collapse-item" href="{{route('bundles.create')}}">@lang('sidebar.add_bundles')</a>

            </div>
        </div>
    </li>
    <!-- Categories -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#categoryCollapse"
           aria-expanded="true" aria-controls="categoryCollapse">
            <i class="fas fa-sitemap"></i>
            <span>@lang('sidebar.category')</span>
        </a>
        <div id="categoryCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.category_options'):</h6>
                <a class="collapse-item" href="{{route('categories.index')}}">@lang('sidebar.category')</a>
                <a class="collapse-item" href="{{route('categories.create')}}">@lang('sidebar.add_category')</a>
            </div>
        </div>
    </li>
    {{-- Products --}}
    {{-- Bandle --}}
    <li class="nav-item">
        <a class="nav-link" href="{{route('coupons.index')}}">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.coupons')</span></a>
    </li>
    <!-- Comments -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('comments.index')}}">
            <i class="fas fa-comments fa-chart-area"></i>
            <span>@lang('sidebar.comments')</span>
        </a>
    </li>

    {{-- Products --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#productCollapse"
           aria-expanded="true" aria-controls="productCollapse">
            <i class="fas fa-cubes"></i>
            <span>@lang('sidebar.products')</span>
        </a>
        <div id="productCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.product_options'):</h6>
                <a class="collapse-item" href="{{route('products.index')}}">@lang('sidebar.products')</a>
                <a class="collapse-item" href="{{route('products.create')}}">@lang('sidebar.add_product')</a>
                <a class="collapse-item" href="{{route('export-import-product.index')}}">CSV Import & Export</a>
            </div>
        </div>
    </li>

    {{-- Shipping --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#shippingCollapse"
           aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-truck"></i>
            <span>@lang('sidebar.shipping')</span>
        </a>
        <div id="shippingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.shipping_options'):</h6>
                <a class="collapse-item" href="{{route('shipping.index')}}">@lang('sidebar.shipping')</a>
                <a class="collapse-item" href="{{route('shipping.create')}}">@lang('sidebar.add_shipping')</a>
            </div>
        </div>
    </li>
    {{-- Nwesletter --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#newsletterCollapse"
           aria-expanded="true" aria-controls="newsletterCollapse">
            <i class="fas fa-truck"></i>
            <span>@lang('sidebar.newsletters')</span>
        </a>
        <div id="newsletterCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.newsletters_options'):</h6>
                <a class="collapse-item" href="{{route('newsletters.index')}}">@lang('sidebar.newsletters')</a>
                <a class="collapse-item" href="{{route('newsletters.create')}}">@lang('sidebar.add_newsletter')</a>
            </div>
        </div>
    </li>
    @endhasrole

    <!--Orders -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('orders.index')}}">
            <i class="fas fa-hammer fa-chart-area"></i>
            <span>@lang('sidebar.orders')</span>
        </a>
    </li>

    <!-- Reviews -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('reviews.index')}}">
            <i class="fas fa-comments"></i>
            <span>@lang('sidebar.reviews')</span></a>
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
            <span>@lang('sidebar.posts')</span>
        </a>
        <div id="postCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.post_options'):</h6>
                <a class="collapse-item" href="{{route('posts.index')}}">@lang('sidebar.posts')</a>
                <a class="collapse-item" href="{{route('posts.create')}}">@lang('sidebar.add_post')</a>
            </div>
        </div>
    </li>

    <!-- Tags -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#tagCollapse" aria-expanded="true"
           aria-controls="tagCollapse">
            <i class="fas fa-tags fa-folder"></i>
            <span>@lang('sidebar.tags')</span>
        </a>
        <div id="tagCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.tags_options'):</h6>
                <a class="collapse-item" href="{{route('tags.index')}}">@lang('sidebar.tags')</a>
                <a class="collapse-item" href="{{route('tags.create')}}">@lang('sidebar.add_tag')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('coupons.index')}}">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.coupons')</span></a>
    </li>
    @endhasrole
    <!-- Comments -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('comments.index')}}">
            <i class="fas fa-comments fa-chart-area"></i>
            <span>@lang('sidebar.comments')</span>
        </a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Heading -->
    <div class="sidebar-heading">
        @lang('sidebar.general_settings')
    </div>

    <!-- Users -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#userCollapse"
           aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-wrench"></i>
            <span>@lang('sidebar.users')</span>
        </a>
        <div id="userCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.user_options'):</h6>
                <a class="collapse-item" href="{{route('users.index')}}">@lang('sidebar.users')</a>
                @hasrole('super-admin')
                <a class="collapse-item" href="{{route('roles.index')}}">@lang('sidebar.roles')</a>
                <a class="collapse-item" href="{{route('permissions.index')}}">@lang('sidebar.permissions')</a>
                @endhasrole
            </div>
        </div>
    </li>
    @hasrole('super-admin')
    <!-- General settings -->
    {{-- Config --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#configCollapse"
           aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-wrench"></i>
            <span>@lang('sidebar.configuration')</span>
        </a>
        <div id="configCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.configuration_options'):</h6>
                <a class="collapse-item" href="{{route('settings.index')}}">@lang('sidebar.settings')</a>
                <a class="collapse-item" href="{{route('all.notification')}}">@lang('sidebar.notifications')</a>
                <a class="collapse-item"
                   href="{{route('laravelblocker::blocker.index')}}">@lang('sidebar.blocked_ip')</a>
                <a class="collapse-item" href="{{route('activity')}}">@lang('sidebar.activity_log')</a>
                <a class="collapse-item" href="{{ url('translations/') }}">@lang('sidebar.translation')</a>
            </div>
        </div>
    </li>
    @endhasrole

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>


</ul>
