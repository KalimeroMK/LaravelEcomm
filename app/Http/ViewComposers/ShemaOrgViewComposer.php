<?php

  namespace App\Http\ViewComposers;

  use Illuminate\View\View;
  use Spatie\SchemaOrg\Schema;

  class ShemaOrgViewComposer
  {
    public function compose(View $view)
    {
      $localBusiness = Schema::localBusiness()
          ->name('EcommLaravel')
          ->email('zbogoevski@gmail.com')
          ->contactPoint(Schema::contactPoint()->areaServed('Worldwide'));
      $view->with('shema', $localBusiness->toScript());
    }
  }