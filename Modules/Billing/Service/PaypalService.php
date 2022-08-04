<?php

namespace Modules\Billing\Service;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use JetBrains\PhpStorm\NoReturn;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Srmklive\PayPal\Facades\PayPal;
use Throwable;

class PaypalService
{
    
    /**
     * @return Application|RedirectResponse|Redirector
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception|Throwable
     */
    public function payment(): Redirector|RedirectResponse|Application
    {
        $cart = Cart::whereUserId(auth()->user()->id)->whereOrderId(null)->get()->toArray();
        
        $data = [];
        
        $data['items'] = array_map(function ($item) use ($cart) {
            $name = Product::whereId($item['product_id'])->pluck('title');
            
            return [
                'name'  => $name,
                'price' => $item['price'],
                'desc'  => 'Thank you for using paypal',
                'qty'   => $item['quantity'],
            ];
        }, $cart);
        
        $data['invoice_id']          = 'ORD-'.strtoupper(uniqid());
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
        $data['return_url']          = route('payment.success');
        $data['cancel_url']          = route('payment.cancel');
        
        $data['total'] = session()->get('total');
        
        Cart::whereUserId(Auth()->id())->whereOrderId(null)
            ->update(['order_id' => session()->get('id')]);
        // Init PayPal
        $provider = PayPal::setProvider();
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);
        $response = $provider->createOrder($data, true);
        
        return redirect($response['paypal_link']);
    }
    
    /**
     * Responds with a welcome message with instructions
     *
     * @return Response
     */
    #[NoReturn] public function cancel(): Response
    {
        dd('Your payment is canceled. You can create cancel page here.');
    }
    
    /**
     * Responds with a welcome message with instructions
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function success(): RedirectResponse
    {
        // Init PayPal
        $provider = PayPal::setProvider();
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);
        
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            request()->session()->flash('success', 'You successfully pay from Paypal! Thank You');
            session()->forget('cart');
            session()->forget('coupon');
            
            return redirect()->route('home');
        }
        
        request()->session()->flash('error', 'Something went wrong please try again!!!');
        
        return redirect()->back();
    }
}