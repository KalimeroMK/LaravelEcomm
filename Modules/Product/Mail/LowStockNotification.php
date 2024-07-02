<?php

namespace Modules\Product\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection; // Ensure you import Collection class

class LowStockNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $products;

    /**
     * Create a new message instance.
     *
     * @param  Collection  $products
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
    public function build()
    {
        return $this->subject('Low Stock Alert')
            ->from('your-email@example.com', 'Your Name')
            ->view('product::emails.low_stock');
    }
}
