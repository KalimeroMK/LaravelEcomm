<?php

    namespace App\Http\Controllers;

    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;
    use Stripe\Charge;
    use Stripe\Exception\ApiErrorException;
    use Stripe\Stripe;

    class StripeController extends Controller
    {
        /**
         * success response method.
         * @return Application|Factory|View
         */

        public function stripe()
        {
            return view('frontend.pages.stripe');
        }

        /**
         * success response method.
         *
         * @param  Request  $request
         * @return RedirectResponse
         * @throws ApiErrorException
         */

        public function stripePost(Request $request): RedirectResponse
        {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            Charge::create([
                "amount"      => 100 * 100,
                "currency"    => "usd",
                "source"      => $request->stripeToken,
                "description" => "Test payment from zbogoevski@gmail.com.",
            ]);

            Session::flash('success', 'Payment successful!');
            return back();
        }
    }
