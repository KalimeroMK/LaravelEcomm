<?php

namespace Modules\Newsletter\Console;

use Illuminate\Console\Command;
use Modules\Newsletter\Jobs\ProductNewsletterJob;
use Modules\Newsletter\Models\Newsletter;
use Modules\Product\Models\Product;

class ProductNewsletterCommand extends Command
{
    protected $signature = 'newsletter:product';

    protected $description = 'Send newsletters with the latest products';

    public function handle(): void
    {
        // Get the latest 10 products
        $products = Product::orderBy('id', 'asc')
            ->take(10)
            ->get()
            ->all(); // Convert collection to an array

        // Get validated newsletters
        $newsletters = Newsletter::whereIsValidated(true)->get();

        // Dispatch the newsletter jobs
        foreach ($newsletters as $newsletter) {
            ProductNewsletterJob::dispatch($newsletter->email, $products);
        }

        $this->info('Product newsletter jobs dispatched successfully.');
    }
}
