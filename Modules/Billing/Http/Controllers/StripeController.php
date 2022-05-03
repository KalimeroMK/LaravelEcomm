<?php

    namespace Modules\Billing\Http\Controllers;

    use App\Helpers\Payment;
    use App\Http\Controllers\Controller;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use JetBrains\PhpStorm\Pure;
    use Modules\Cart\Models\Cart;
    use Stripe\Charge;
    use Stripe\Exception\ApiErrorException;
    use Stripe\Stripe;

    class StripeController extends Controller
    {
        public Payment $payment;

        #[Pure] public function __construct()
        {
            $this->payment = new Payment();
        }

        /**
         * success response method.
         * @return Application|Factory|View
         */

        public function stripe($id)
        {
            return view('front::pages.stripe', compact('id'));
        }

        /**
         * success response method.
         *
         * @param  Request  $request
         *
         * @return RedirectResponse
         * @throws ApiErrorException
         */

        public function stripePost(Request $request): RedirectResponse
        {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $data = $this->payment->calculate($request);

            Charge::create([
                "amount"      => $data[1] * 100,
                "currency"    => "usd",
                "source"      => $request->stripeToken,
                "description" => "Test payment from zbogoevski@gmail.com.",
            ]);
            Cart::where('user_id', Auth::id())->where('order_id', null)->update(['order_id' => $data[0]]);
            Session::flash('success', 'Payment successful!');

            return back();
        }
    }
