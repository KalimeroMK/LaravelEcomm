<?php

    namespace Modules\Product\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Notifications\StatusNotification;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Modules\Product\Http\Requests\ProductReviewStore;
    use Modules\Product\Models\Product;
    use Modules\Product\Models\ProductReview;
    use Modules\User\Models\User;
    use Notification;

    class ProductReviewController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return Application|Factory|View
         */
        public function index()
        {
            $reviews = ProductReview::getAllReview();

            return view('product::review.index', compact('reviews'));
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  ProductReviewStore  $request
         *
         * @return RedirectResponse
         */
        public function store(ProductReviewStore $request): RedirectResponse
        {
            $product_info       = Product::getProductBySlug($request['slug']);
            $data               = $request->all();
            $data['product_id'] = $product_info->id;
            $data['user_id']    = $request->user()->id;
            $data['status']     = 'active';
            // dd($data)q;
            $status  = ProductReview::create($data);
            $details = [
                'title'     => 'New Product Rating!',
                'actionURL' => route('product-detail', $product_info->slug),
                'fas'       => 'fa-star',
            ];
            Notification::send(User::role('super-admin')->get(), new StatusNotification($details));
            if ($status) {
                request()->session()->flash('success', 'Thank you for your feedback');
            } else {
                request()->session()->flash('error', 'Something went wrong! Please try again!!');
            }

            return redirect()->back();
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  ProductReview  $productReview
         *
         * @return Application|Factory|View
         */
        public function edit(ProductReview $productReview)
        {
            // return $review;
            return view('product::review.edit', compact('productReview'));
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  Request  $request
         * @param  int  $id
         *
         * @return RedirectResponse
         */
        public function update(Request $request, int $id): RedirectResponse
        {
            $review = ProductReview::findOrFail($id);
            if ($review) {
                $status = $review->update($request->all());
                if ($status) {
                    request()->session()->flash('success', 'Review Successfully updated');
                } else {
                    request()->session()->flash('error', 'Something went wrong! Please try again!!');
                }
            } else {
                request()->session()->flash('error', 'Review not found!!');
            }

            return redirect()->route('product::review.index');
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param  int  $id
         *
         * @return RedirectResponse
         */
        public function destroy(int $id): RedirectResponse
        {
            $review = ProductReview::find($id);
            $status = $review->delete();
            if ($status) {
                request()->session()->flash('success', 'Successfully deleted review');
            } else {
                request()->session()->flash('error', 'Something went wrong! Try again');
            }

            return redirect()->route('review.index');
        }
    }
