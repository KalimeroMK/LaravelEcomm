<?php

namespace Modules\Newsletter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\Newsletter\Mail\ProductNewsletterMail;
use Modules\Newsletter\Models\Newsletter;
use Modules\Product\Models\Product;

class ProductNewsletterCommand extends Command
{
    protected $signature = 'newsletter:product';

    protected $description = 'Command description';

    public function handle(): void
    {
        $products = Product::orderBy('id', 'asc')
            ->take(10)
            ->get();
        $newsletters = Newsletter::whereIsValidated(true)->get();
        foreach ($newsletters as $newsletter) {
            Mail::to($newsletter->email)->send(new ProductNewsletterMail($products));
        }
    }
}
