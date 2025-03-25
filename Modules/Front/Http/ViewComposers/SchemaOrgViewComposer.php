<?php

declare(strict_types=1);

namespace Modules\Front\Http\ViewComposers;

use Illuminate\View\View;
use Spatie\SchemaOrg\Schema;

class SchemaOrgViewComposer
{
    public function compose(View $view): void
    {
        $localBusiness = Schema::localBusiness()
            ->name('EcommLaravel')
            ->email('zbogoevski@gmail.com')
            ->contactPoint(Schema::contactPoint()->areaServed('Worldwide'));
        $view->with('schema', $localBusiness->toScript());
    }
}
