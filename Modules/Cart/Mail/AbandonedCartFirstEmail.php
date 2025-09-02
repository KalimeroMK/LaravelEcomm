<?php

declare(strict_types=1);

namespace Modules\Cart\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Cart\Models\AbandonedCart;

class AbandonedCartFirstEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public AbandonedCart $abandonedCart;

    public function __construct(AbandonedCart $abandonedCart)
    {
        $this->abandonedCart = $abandonedCart;
    }

    public function build(): self
    {
        return $this
            ->subject('You left something in your cart! 🛒')
            ->view('cart::emails.abandoned-cart-first')
            ->with([
                'abandonedCart' => $this->abandonedCart,
                'cartItems' => $this->abandonedCart->cart_items,
                'userName' => $this->abandonedCart->user?->name ?? 'Valued Customer',
                'cartUrl' => route('front.cart'),
                'unsubscribeUrl' => route('newsletter.unsubscribe', ['email' => $this->abandonedCart->email]),
            ]);
    }
}
