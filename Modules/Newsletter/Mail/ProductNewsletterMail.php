<?php

namespace Modules\Newsletter\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductNewsletterMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function build(): ProductNewsletterMail
    {
        return $this->markdown('newsletter::emails.product-newsletter')->with('products', $this->products);
    }
}
