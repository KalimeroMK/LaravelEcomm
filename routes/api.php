<?php

declare(strict_types=1);

// Load module API routes (Product, Tag, Shipping, etc.)
foreach (glob(base_path('Modules/*/Routes/api.php')) as $routeFile) {
    require $routeFile;
}
