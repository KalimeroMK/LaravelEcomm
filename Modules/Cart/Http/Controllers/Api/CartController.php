<?php

namespace Modules\Cart\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Cart\Http\Requests\AddToCartSingle;
use Modules\Cart\Http\Resources\CartResource;
use Modules\Cart\Service\CartService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;

class CartController extends CoreController
{
    private CartService $cart_service;
    
    public function __construct(CartService $cart_service)
    {
        $this->cart_service = $cart_service;
    }
    
    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return CartResource::collection($this->cart_service->getAll());
    }
    
    /**
     * @param  AddToCartSingle  $request
     *
     * @return JsonResponse|string
     */
    public function store(AddToCartSingle $request)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.storeSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->cart_service->cart_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new CartResource($this->cart_service->apiAddToCart($request->all())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return JsonResponse|string
     */
    public function show()
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.ok',
                        [
                            'resource' => Helper::getResourceName(
                                $this->cart_service->cart_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new CartResource($this->cart_service->show()));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param  AddToCartSingle  $request
     *
     * @return JsonResponse|string
     */
    public function update(AddToCartSingle $request)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.updateSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->cart_service->cart_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new CartResource($this->cart_service->apiAUpdateCart($request->all())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function destroy($id)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.deleteSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->cart_service->cart_repository->model
                            ),
                        ]
                    )
                )
                ->respond($this->cart_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
