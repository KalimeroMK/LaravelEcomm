<?php

    namespace Modules\Brand\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\JsonResponse;
    use Modules\Brand\Http\Requests\Api\StoreRequest;
    use Modules\Brand\Http\Requests\Api\UpdateRequest;
    use Modules\Brand\Models\Brand;
    use Modules\Brand\Repository\BrandRepository;

    class BrandController extends Controller
    {
        private BrandRepository $brand;

        public function __construct(BrandRepository $brand)
        {
            $this->brand = $brand;
        }

        /**
         * Display a listing of the resource.
         *
         * @return JsonResponse
         */
        public function index(): JsonResponse
        {
            return $this->sendResponse([$this->brand->getAll()], 200);
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  StoreRequest  $request
         *
         * @return JsonResponse
         */
        public function store(StoreRequest $request): JsonResponse
        {
            $data = $request->all();

            return $this->sendResponse([$this->brand->storeBrand($data)], 200);
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  $id
         *
         * @return JsonResponse
         */
        public function show($id): JsonResponse
        {
            return $this->sendResponse([Brand::findOrFail($id)], 200);
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  UpdateRequest  $request
         * @param  Brand  $brand
         *
         * @return JsonResponse
         */
        public function update(UpdateRequest $request, Brand $brand): JsonResponse
        {
            $data = $request->all();

            return $this->sendResponse([$this->brand->updateBrand($data, $brand->id)], 200);
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param $id
         *
         * @return JsonResponse
         */
        public function destroy($id): JsonResponse
        {
            return $this->sendResponse([$this->brand->deleteBrand($id)], 200);
        }
    }
