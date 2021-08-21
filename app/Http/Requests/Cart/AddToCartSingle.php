<?php

  namespace App\Http\Requests\Cart;

  use Illuminate\Foundation\Http\FormRequest;

  class AddToCartSingle extends FormRequest
  {
    /**
     * @var mixed
     */
    public $slug;
    /**
     * @var mixed
     */
    public $quant;

    public function rules(): array
    {
      return [
          'slug'  => 'required|string',
          'quant' => 'required|integer',
      ];
    }

    public function authorize(): bool
    {
      return true;
    }
  }
