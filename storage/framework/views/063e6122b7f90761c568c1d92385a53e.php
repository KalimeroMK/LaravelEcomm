<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo e(route('admin')); ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">
            <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>
            <?php echo app('translator')->get('sidebar.admin'); ?>
            <?php else: ?>
            <?php echo e(Auth::user()->name ?? 'Account'); ?>

            <?php endif; ?>
        </div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="<?php echo e(route('admin')); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span><?php echo app('translator')->get('sidebar.dashboard'); ?></span></a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider">

    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>
    <!-- Analytics Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('admin.analytics')); ?>">
            <i class="fas fa-chart-line"></i>
            <span><?php echo app('translator')->get('sidebar.analytics_dashboard'); ?></span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    <?php endif; ?>

    <div class="sidebar-heading">
        <?php echo app('translator')->get('sidebar.shop'); ?>
    </div>
    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>
    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#attributeCollapse"
           aria-expanded="true" aria-controls="attributeCollapse">
            <i class="fas fa-cubes"></i>
            <span><?php echo app('translator')->get('sidebar.attributes'); ?></span>
        </a>
        <div id="attributeCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.attribute_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('attributes.index')); ?>"><?php echo app('translator')->get('sidebar.attributes'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('attributes.create')); ?>"><?php echo app('translator')->get('sidebar.add_attribute'); ?></a>
                <a class="collapse-item"
                   href="<?php echo e(route('attribute_groups.index')); ?>"><?php echo app('translator')->get('sidebar.attribute_groups'); ?></a>
            </div>
        </div>
    </li>
    
    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bannerCollapse" aria-expanded="true"
           aria-controls="brandCollapse">
            <i class="fas fa-table"></i>
            <span><?php echo app('translator')->get('sidebar.banners'); ?></span>
        </a>
        <div id="bannerCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.brand_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('banners.index')); ?>"><?php echo app('translator')->get('sidebar.banners'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('banners.create')); ?>"><?php echo app('translator')->get('sidebar.add_banners'); ?></a>
            </div>
        </div>
    </li>
    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#brandCollapse" aria-expanded="true"
           aria-controls="brandCollapse">
            <i class="fas fa-table"></i>
            <span><?php echo app('translator')->get('sidebar.brands'); ?></span>
        </a>
        <div id="brandCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.brand_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('brands.index')); ?>"><?php echo app('translator')->get('sidebar.brands'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('brands.create')); ?>"><?php echo app('translator')->get('sidebar.add_brand'); ?></a>
            </div>
        </div>
    </li>
    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bundleCollapse"
           aria-expanded="true" aria-controls="bundleCollapse">
            <i class="fas fa-cubes"></i>
            <span><?php echo app('translator')->get('sidebar.bundles'); ?></span>
        </a>
        <div id="bundleCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.bundles_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('bundles.index')); ?>"><?php echo app('translator')->get('sidebar.bundles'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('bundles.create')); ?>"><?php echo app('translator')->get('sidebar.add_bundles'); ?></a>

            </div>
        </div>
    </li>
    <!-- Categories -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#categoryCollapse"
           aria-expanded="true" aria-controls="categoryCollapse">
            <i class="fas fa-sitemap"></i>
            <span><?php echo app('translator')->get('sidebar.category'); ?></span>
        </a>
        <div id="categoryCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.category_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('categories.index')); ?>"><?php echo app('translator')->get('sidebar.category'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('categories.create')); ?>"><?php echo app('translator')->get('sidebar.add_category'); ?></a>
            </div>
        </div>
    </li>
    
    
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('admin.coupons.index')); ?>">
            <i class="fas fa-table"></i>
            <span><?php echo app('translator')->get('sidebar.coupons'); ?></span></a>
    </li>
    <!-- Comments -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('comments.index')); ?>">
            <i class="fas fa-comments fa-chart-area"></i>
            <span><?php echo app('translator')->get('sidebar.comments'); ?></span>
        </a>
    </li>

    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#productCollapse"
           aria-expanded="true" aria-controls="productCollapse">
            <i class="fas fa-cubes"></i>
            <span><?php echo app('translator')->get('sidebar.products'); ?></span>
        </a>
        <div id="productCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.product_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('admin.products.index')); ?>"><?php echo app('translator')->get('sidebar.products'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('admin.products.create')); ?>"><?php echo app('translator')->get('sidebar.add_product'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('export-import-product.index')); ?>"><?php echo app('translator')->get('sidebar.csv_import_export'); ?></a>
            </div>
        </div>
    </li>

    
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('product-stats.index')); ?>">
            <i class="fas fa-chart-bar"></i>
            <span><?php echo app('translator')->get('sidebar.product_stats'); ?></span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#shippingCollapse"
           aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-truck"></i>
            <span><?php echo app('translator')->get('sidebar.shipping'); ?></span>
        </a>
        <div id="shippingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.shipping_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('admin.shipping.index')); ?>"><?php echo app('translator')->get('sidebar.shipping'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('admin.shipping.create')); ?>"><?php echo app('translator')->get('sidebar.add_shipping'); ?></a>
            </div>
        </div>
    </li>
    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#newsletterCollapse"
           aria-expanded="true" aria-controls="newsletterCollapse">
            <i class="fas fa-envelope"></i>
            <span><?php echo app('translator')->get('sidebar.newsletters'); ?></span>
        </a>
        <div id="newsletterCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.newsletters_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('newsletters.index')); ?>"><?php echo app('translator')->get('sidebar.newsletters'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('newsletters.create')); ?>"><?php echo app('translator')->get('sidebar.add_newsletter'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('admin.email-templates.index')); ?>"><?php echo app('translator')->get('sidebar.email_templates'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('admin.email-templates.create')); ?>"><?php echo app('translator')->get('sidebar.create_template'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('admin.email-campaigns.index')); ?>"><?php echo app('translator')->get('sidebar.email_campaigns'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('admin.email-campaigns.create')); ?>"><?php echo app('translator')->get('sidebar.create_campaign'); ?></a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!--Orders -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('orders.index')); ?>">
            <i class="fas fa-hammer fa-chart-area"></i>
            <span><?php echo app('translator')->get('sidebar.orders'); ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('order-returns.index')); ?>">
            <i class="fas fa-undo fa-chart-area"></i>
            <span><?php echo app('translator')->get('sidebar.returns'); ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('admin.complaints.index')); ?>">
            <i class="fas fa-question fa-chart-area"></i>
            <span><?php echo app('translator')->get('sidebar.complaints'); ?></span>
        </a>
    </li>
    <!-- Reviews -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('reviews.index')); ?>">
            <i class="fas fa-comments"></i>
            <span><?php echo app('translator')->get('sidebar.reviews'); ?></span></a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider">

    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>
    <!-- Heading -->
    <div class="sidebar-heading">
        <?php echo app('translator')->get('sidebar.marketing'); ?>
    </div>

    <!-- Email Marketing -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#emailMarketingCollapse" aria-expanded="true"
           aria-controls="emailMarketingCollapse">
            <i class="fas fa-envelope"></i>
            <span><?php echo app('translator')->get('sidebar.email_marketing'); ?></span>
        </a>
        <div id="emailMarketingCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.email_marketing_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('newsletters.index')); ?>"><?php echo app('translator')->get('sidebar.newsletters'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('newsletters.create')); ?>"><?php echo app('translator')->get('sidebar.create_campaign'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('admin.email-campaigns.analytics')); ?>"><?php echo app('translator')->get('sidebar.email_analytics'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('admin.email-campaigns.index')); ?>"><?php echo app('translator')->get('sidebar.campaigns'); ?></a>
            </div>
        </div>
    </li>

    <!-- Abandoned Cart -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('admin.analytics.abandoned-carts')); ?>">
            <i class="fas fa-shopping-cart"></i>
            <span><?php echo app('translator')->get('sidebar.abandoned_carts'); ?></span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        <?php echo app('translator')->get('sidebar.posts'); ?>
    </div>
    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>

    <!-- Products -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#postCollapse" aria-expanded="true"
           aria-controls="postCollapse">
            <i class="fas fa-fw fa-folder"></i>
            <span><?php echo app('translator')->get('sidebar.posts'); ?></span>
        </a>
        <div id="postCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.post_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('posts.index')); ?>"><?php echo app('translator')->get('sidebar.posts'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('posts.create')); ?>"><?php echo app('translator')->get('sidebar.add_post'); ?></a>
            </div>
        </div>
    </li>

    <!-- Tags -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#tagCollapse" aria-expanded="true"
           aria-controls="tagCollapse">
            <i class="fas fa-tags fa-folder"></i>
            <span><?php echo app('translator')->get('sidebar.tags'); ?></span>
        </a>
        <div id="tagCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.tags_options'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('tags.index')); ?>"><?php echo app('translator')->get('sidebar.tags'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('tags.create')); ?>"><?php echo app('translator')->get('sidebar.add_tag'); ?></a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('admin.coupons.index')); ?>">
            <i class="fas fa-table"></i>
            <span><?php echo app('translator')->get('sidebar.coupons'); ?></span></a>
    </li>
    <?php endif; ?>
    <!-- Comments -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('comments.index')); ?>">
            <i class="fas fa-comments fa-chart-area"></i>
            <span><?php echo app('translator')->get('sidebar.comments'); ?></span>
        </a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>
    <!-- Heading -->
    <div class="sidebar-heading">
        <?php echo app('translator')->get('sidebar.seo_performance'); ?>
    </div>

    <!-- SEO Tools -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#seoCollapse" aria-expanded="true"
           aria-controls="seoCollapse">
            <i class="fas fa-search"></i>
            <span><?php echo app('translator')->get('sidebar.seo_tools'); ?></span>
        </a>
        <div id="seoCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.seo_options'); ?>:</h6>
                <a class="collapse-item" href="/sitemap.xml"><?php echo app('translator')->get('sidebar.xml_sitemap'); ?></a>
                <a class="collapse-item" href="/robots.txt"><?php echo app('translator')->get('sidebar.robots_txt'); ?></a>
                <a class="collapse-item" href="#" onclick="generateSitemap()"><?php echo app('translator')->get('sidebar.generate_sitemap'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('settings.seo.index')); ?>"><?php echo app('translator')->get('sidebar.meta_tags'); ?></a>
            </div>
        </div>
    </li>

    <!-- Performance -->
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="clearCache()">
            <i class="fas fa-tachometer-alt"></i>
            <span><?php echo app('translator')->get('sidebar.clear_cache'); ?></span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Heading -->
    <div class="sidebar-heading">
        <?php echo app('translator')->get('sidebar.general_settings'); ?>
    </div>

    <!-- Users -->
    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>
    <!-- General settings -->
    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#configCollapse"
           aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-wrench"></i>
            <span><?php echo app('translator')->get('sidebar.configuration'); ?></span>
        </a>
        <div id="configCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header"><?php echo app('translator')->get('sidebar.configuration'); ?>:</h6>
                <a class="collapse-item" href="<?php echo e(route('users.index')); ?>"><?php echo app('translator')->get('sidebar.users'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('roles.index')); ?>"><?php echo app('translator')->get('sidebar.roles'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('permissions.index')); ?>"><?php echo app('translator')->get('sidebar.permissions'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('settings.index')); ?>"><?php echo app('translator')->get('sidebar.settings'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('settings.payment.index')); ?>"><?php echo app('translator')->get('sidebar.payment_settings'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('settings.shipping.index')); ?>"><?php echo app('translator')->get('sidebar.shipping_settings'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('settings.email.index')); ?>"><?php echo app('translator')->get('sidebar.email_settings'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('settings.seo.index')); ?>"><?php echo app('translator')->get('sidebar.seo_settings'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('pages.index')); ?>"><?php echo app('translator')->get('sidebar.page'); ?></a>
                <?php if(config('tenant.multi_tenant.enabled')): ?>
                    <a class="collapse-item" href="<?php echo e(route('tenant.index')); ?>"><?php echo app('translator')->get('sidebar.tenant'); ?></a>
                <?php endif; ?>
                <a class="collapse-item"
                   href="javascript:void(0);"><?php echo app('translator')->get('sidebar.blocked_ip'); ?></a>
                <a class="collapse-item" href="<?php echo e(route('activity')); ?>"><?php echo app('translator')->get('sidebar.activity_log'); ?></a>
                <a class="collapse-item" href="<?php echo e(url('translations/')); ?>"><?php echo app('translator')->get('sidebar.translation'); ?></a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>


</ul>

<script>
// SEO and Performance Functions
function generateSitemap() {
    if (confirm('Generate XML Sitemap? This may take a few minutes.')) {
        fetch('/api/v1/admin/seo/generate-sitemap', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Sitemap generated successfully!');
        })
        .catch(error => {
            alert('Error generating sitemap: ' + error.message);
        });
    }
}

function clearCache() {
    if (confirm('Clear all application cache? This will improve performance but may slow down the next few requests.')) {
        fetch('/api/v1/admin/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Cache cleared successfully!');
        })
        .catch(error => {
            alert('Error clearing cache: ' + error.message);
        });
    }
}
</script>
<?php /**PATH /var/www/Modules/Admin/Resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>