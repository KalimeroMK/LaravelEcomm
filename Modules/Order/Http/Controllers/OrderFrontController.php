<?php

    namespace Modules\Order\Http\Controllers;

    use App\Helpers\Payment;
    use App\Http\Controllers\Controller;
    use Carbon\Carbon;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Modules\Cart\Models\Cart;
    use Modules\Order\Models\Order;
    use PDF;

    class OrderFrontController extends Controller
    {
        public Payment $payment;

        public function __construct()
        {
            $this->payment = new Payment();
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  Request  $request
         *
         * @return RedirectResponse
         */
        public function store(Request $request): RedirectResponse
        {
            $data = $this->payment->calculate($request);

            if (request('payment_method') == 'paypal') {
                return redirect()->route('payment')->with($data[0], $data[1]);
            } elseif (request('payment_method') == 'stripe') {
                return redirect()->route('stripe')->with($data);
            } else {
                session()->forget('cart');
                session()->forget('coupon');
            }
            Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $data[0]]);

            request()->session()->flash('success', 'Your product successfully placed in order');

            return redirect()->route('home');
        }

        /**
         * @return Application|Factory|View
         *
         */
        public function orderTrack()
        {
            return view('frontend.pages.order-track');
        }

        /**
         * @param  Request  $request
         *
         * @return RedirectResponse
         */
        public function productTrackOrder(Request $request): RedirectResponse
        {
            $order = Order::where('user_id', auth()->user()->id)->where(
                'order_number',
                $request->order_number
            )->first();
            if ($order) {
                if ($order->status == "new") {
                    request()->session()->flash('success', 'Your order has been placed. please wait.');

                    return redirect()->route('home');
                } elseif ($order->status == "process") {
                    request()->session()->flash('success', 'Your order is under processing please wait.');

                    return redirect()->route('home');
                } elseif ($order->status == "delivered") {
                    request()->session()->flash('success', 'Your order is successfully delivered.');

                    return redirect()->route('home');
                } else {
                    request()->session()->flash('error', 'Your order canceled. please try again');

                    return redirect()->route('home');
                }
            } else {
                request()->session()->flash('error', 'Invalid order numer please try again');

                return back();
            }
        }
        /**
         * @param  Request  $request
         *
         * @return Response
         */
        // PDF generate
        public function pdf(Request $request): Response
        {
            $order     = Order::getAllOrder($request->id);
            $file_name = $order->order_number.'-'.$order->first_name.'.pdf';
            $pdf       = PDF::loadview('backend.order.pdf', compact('order'));

            return $pdf->download($file_name);
        }
        /**
         * @param  Request  $request
         *
         * @return array
         */
        // Income chart
        public function incomeChart(Request $request): array
        {
            $year   = Carbon::now()->year;
            $items  = Order::with(['cart_info'])->whereYear('created_at', $year)->where('status', 'delivered')->get()
                           ->groupBy(function ($d) {
                               return Carbon::parse($d->created_at)->format('m');
                           });
            $result = [];
            foreach ($items as $month => $item_collections) {
                foreach ($item_collections as $item) {
                    $amount = $item->cart_info->sum('amount');
                    $m      = intval($month);
                    isset($result[$m]) ? $result[$m] += $amount : $result[$m] = $amount;
                }
            }
            $data = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthName        = date('F', mktime(0, 0, 0, $i, 1));
                $data[$monthName] = ( ! empty($result[$i])) ? number_format((float)($result[$i]), 2, '.', '') : 0.0;
            }

            return $data;
        }
    }
