<?php

    namespace Modules\Banner\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\JsonResponse;
    use Modules\Banner\Http\Requests\Api\StoreRequest;
    use Modules\Banner\Http\Requests\Api\UpdateRequest;
    use Modules\Banner\Models\Banner;
    use Modules\Banner\Repository\BannerRepository;

    class BannerController extends Controller
    {
        private BannerRepository $banner;

        public function __construct(BannerRepository $banner)
        {
            $this->banner = $banner;
        }

        /**
         * @return JsonResponse
         */
        public function index(): JsonResponse
        {
            return $this->sendResponse([$this->banner->getAll()], 200);
        }

        /**
         * @param  StoreRequest  $request
         *
         * @return JsonResponse
         */
        public function store(StoreRequest $request): JsonResponse
        {
            $data  = $request->except('photo');
            $image = $request['photo'];

            return $this->sendResponse([$this->banner->storeBanner($data, $image)], 200);
        }

        /**
         * @param $id
         *
         * @return JsonResponse
         */
        public function show($id): JsonResponse
        {
            return $this->sendResponse([$this->banner->getById($id)], 200);
        }

        /**
         * @param  UpdateRequest  $request
         * @param $id
         *
         * @return JsonResponse
         */
        public function update(UpdateRequest $request, $id): JsonResponse
        {
            $banner = Banner::findOrFail($id);
            $data   = $request->except('photo');
            $image  = $request['photo'];

            return $this->sendResponse([$this->banner->updateBanner($data, $image, $banner->id)], 200);
        }

        /**
         * @param $id
         *
         * @return JsonResponse
         */
        public function destroy($id): JsonResponse
        {
            return $this->sendResponse([$this->banner->deleteBanner($id)], 200);
        }
    }
