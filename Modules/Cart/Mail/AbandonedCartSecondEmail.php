<?php

declare(strict_types=1);

namespace Modules\Cart\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Cart\Models\AbandonedCart;

class AbandonedCartSecondEmail extends Mailable implements ShouldQueue
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
            ->subject('Still thinking? Here\'s a special offer! 💰')
            ->view('cart::emails.abandoned-cart-second')
            ->with([
                'abandonedCart' => $this->abandonedCart,
                'cartItems' => $this->abandonedCart->cart_items,
                'userName' => $this->abandonedCart->user?->name ?? 'Valued Customer',
                'cartUrl' => route('front.cart', ['locale' => app()->getLocale()]),
                'discountCode' => 'CART10', // You can generate dynamic codes
                'discountPercent' => 10,
                'unsubscribeUrl' => $this->unsubscribeUrl(),
            ]);
    }

    /**
     * Newsletter delete link when the address is a subscriber,
     * otherwise the contact page.
     */
    private function unsubscribeUrl(): string
    {
        $subscriber = \Modules\Newsletter\Models\Newsletter::where('email', $this->abandonedCart->email)->first();

        if ($subscriber && $subscriber->token) {
            return route('delete-newsletter', ['token' => $subscriber->token]);
        }

        return route('front.contact', ['locale' => app()->getLocale()]);
    }
}
