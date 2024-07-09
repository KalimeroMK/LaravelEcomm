<?php

namespace Modules\Newsletter\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Product\Models\Product;

/**
 * Class ProductNewsletterMail
 *
 * @property array<int, Product> $products
 */
class ProductNewsletterMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The products for the newsletter.
     *
     * @var array<int, Product>
     */
    private array $products;

    /**
     * Create a new message instance.
     *
     * @param  array<int, Product>  $products
     */
    public function __construct(array $products)
    {
        $this->products = $products;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->markdown('newsletter::emails.product-newsletter')
            ->with('products', $this->products);
    }
}
