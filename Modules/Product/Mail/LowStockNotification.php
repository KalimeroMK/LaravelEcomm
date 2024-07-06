<?php

namespace Modules\Product\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class LowStockNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Collection<int, mixed>
     */
    public Collection $products;

    /**
     * Create a new message instance.
     *
     * @param Collection<int, mixed> $products
     * @return void
     */
    public function __construct(Collection $products)
    {
        $this->products = $products;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->subject('Low Stock Alert')
            ->from('your-email@example.com', 'Your Name')
            ->view('product::emails.low_stock', ['products' => $this->products]);
    }
}
