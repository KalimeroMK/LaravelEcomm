<?php

  namespace App\Http\Controllers;
  use App\Http\Requests\Cart\AddToCartSingle;
  use Illuminate\Contracts\Foundation\Application;
  use Illuminate\Contracts\View\Factory;
  use Illuminate\Http\RedirectResponse;
  use Illuminate\Http\Request;
  use App\Models\Product;
  use App\Models\Wishlist;
  use App\Models\Cart;
  use Illuminate\Support\Str;

  class CartController extends Controller
  {
    protected ?Product $product = null;

    public function __construct(Product $product){
      $this->product=$product;
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function addToCart(Request $request): RedirectResponse
    {
      if (empty($request->slug)) {
        request()->session()->flash('error','Invalid Products');
        return back();
      }
      $product = Product::whereSlug( $request->slug)->first();
      if (empty($product)) {
        request()->session()->flash('error','Invalid Products');
        return back();
      }

      $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id',null)->where('product_id', $product->id)->first();
      if($already_cart) {
        $already_cart->quantity = $already_cart->quantity + 1;
        $already_cart->amount = $product->price+ $already_cart->amount;
        if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
        $already_cart->save();

      }else{

        $cart = new Cart;
        $cart->user_id = auth()->user()->id;
        $cart->product_id = $product->id;
        $cart->price = ($product->price-($product->price*$product->discount)/100);
        $cart->quantity = 1;
        $cart->amount=$cart->price*$cart->quantity;
        if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
        $cart->save();
        Wishlist::where('user_id',auth()->user()->id)->where('cart_id',null)->update(['cart_id'=>$cart->id]);
      }
      request()->session()->flash('success','Product successfully added to cart');
      return back();
    }

    /**
     * @param  AddToCartSingle  $request
     * @return RedirectResponse
     */
    public function singleAddToCart(AddToCartSingle $request): RedirectResponse
    {

      $product = Product::whereSlug($request->slug)->first();
      if($product->stock <$request->quant[1]){
        return back()->with('error','Out of stock, You can add other products.');
      }
      if ( ($request->quant[1] < 1) || empty($product) ) {
        request()->session()->flash('error','Invalid Products');
        return back();
      }

      $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id',null)->where('product_id', $product->id)->first();

      if($already_cart) {
        $already_cart->quantity = $already_cart->quantity + $request->quant[1];
        $already_cart->amount = ($product->price * $request->quant[1])+ $already_cart->amount;

        if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');

        $already_cart->save();

      }else{

        $cart = new Cart;
        $cart->user_id = auth()->user()->id;
        $cart->product_id = $product->id;
        $cart->price = ($product->price-($product->price*$product->discount)/100);
        $cart->quantity = $request->quant[1];
        $cart->amount=($product->price * $request->quant[1]);
        if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
        // return $cart;
        session()->put('cart', $cart);
        $cart->save();
      }

      request()->session()->flash('success','Product successfully added to cart.');
      return back();
    }

    public function cartDelete(Request $request): RedirectResponse
    {
      $cart = Cart::find($request->id);
      if ($cart) {
        $cart->delete();
        request()->session()->flash('success','Cart successfully removed');
        return back();
      }
      request()->session()->flash('error','Error please try again');
      return back();
    }

    public function cartUpdate(Request $request): RedirectResponse
    {
      if($request->quant){
        $error = array();
        $success = '';
        foreach ($request->quant as $k=>$quant) {
          $id = $request->qty_id[$k];
          $cart = Cart::find($id);
          if($quant > 0 && $cart) {

            if($cart->product->stock < $quant){
              request()->session()->flash('error','Out of stock');
              return back();
            }
            $cart->quantity = ($cart->product->stock > $quant) ? $quant  : $cart->product->stock;

            if ($cart->product->stock <=0) continue;
            $after_price=($cart->product->price-($cart->product->price*$cart->product->discount)/100);
            $cart->amount = $after_price * $quant;
            $cart->save();
            session()->put('cart', $cart);
            $success = 'Cart successfully updated!';
          }else{
            $error[] = 'Cart Invalid!';
          }
        }
        return back()->with($error)->with('success', $success);
      }else{
        return back()->with('Cart Invalid!');
      }
    }

    /**
     * @param  Request  $request
     * @return Application|Factory
     */
    public function checkout(Request $request)
    {
       $cart = session('cart');
       $cart_index= Str::random(10);
       $sub_total=0;
           $sub_total+=$cart['amount'];
           $data=[
               'cart_id'=>$cart_index,
               'user_id'=>$request->user()->id,
               'product_id'=>$cart['id'],
               'quantity'=>$cart['quantity'],
               'amount'=>$cart['amount'],
               'status'=>'new',
               'price'=>$cart['price']
           ];

           $cart = new Cart();
           $cart->fill($data);
           $cart->save();
      return view('frontend.pages.checkout');
    }
  }