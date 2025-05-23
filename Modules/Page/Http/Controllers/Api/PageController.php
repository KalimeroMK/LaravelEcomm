<?php

declare(strict_types=1);

namespace Modules\Page\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Page\Actions\CreatePageAction;
use Modules\Page\Actions\DeletePageAction;
use Modules\Page\Actions\GetAllPagesAction;
use Modules\Page\Actions\UpdatePageAction;
use Modules\Page\DTOs\PageDTO;
use Modules\Page\Http\Requests\Api\Store;
use Modules\Page\Http\Requests\Api\Update;
use Modules\Page\Http\Resources\PageResource;
use Modules\Page\Models\Page;

class PageController extends CoreController
{
    public function __construct()
    {
        $this->middleware('permission:page-list', ['only' => ['index']]);
        $this->middleware('permission:page-show', ['only' => ['show']]);
        $this->middleware('permission:page-create', ['only' => ['store']]);
        $this->middleware('permission:page-edit', ['only' => ['update']]);
        $this->middleware('permission:page-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        $pagesDto = (new GetAllPagesAction())->execute();

        return PageResource::collection($pagesDto->pages);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $pageDto = (new CreatePageAction())->execute($request->validated());

        return $this->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Page']))->respond(new PageResource($pageDto));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $page = Page::findOrFail($id);
        $pageDto = new PageDTO($page);

        return $this->setMessage(__('apiResponse.ok', ['resource' => 'Page']))->respond(new PageResource($pageDto));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $pageDto = (new UpdatePageAction())->execute($id, $request->validated());

        return $this->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Page']))->respond(new PageResource($pageDto));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        (new DeletePageAction())->execute($id);

        return $this->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Page']))->respond(null);
    }
}
