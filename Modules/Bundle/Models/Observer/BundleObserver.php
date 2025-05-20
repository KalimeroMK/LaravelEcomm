<?php

namespace Modules\Bundle\Models\Observer;

use Illuminate\Support\Str;
use Modules\Bundle\Models\Bundle;

class BundleObserver
{
    public function creating(Bundle $bundle): void
    {
        $bundle->slug = Str::slug($bundle->name);
    }
}
