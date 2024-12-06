<?php

namespace Modules\Newsletter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Newsletter\Mail\ProductNewsletterMail;

class ProductNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $email;
    public array $products;

    /**
     * Create a new job instance.
     *
     * @param  string  $email
     * @param  array   $products
     */
    public function __construct(string $email, array $products)
    {
        $this->email = $email;
        $this->products = $products;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new ProductNewsletterMail($this->products));
    }
}
