<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Modules\Attribute\Actions\CreateAttributeAction;
use Modules\Attribute\Actions\DeleteAttributeAction;
use Modules\Attribute\Actions\UpdateAttributeAction;
use Modules\Attribute\DTO\AttributeDTO;
use Modules\Attribute\Http\Requests\Attribute\Store;
use Modules\Attribute\Http\Requests\Attribute\Update;
use Modules\Attribute\Models\Attribute;
use Modules\Core\Http\Controllers\CoreController;

class AttributeController extends CoreController
{
    protected CreateAttributeAction $createAction;
    protected UpdateAttributeAction $updateAction;
    protected DeleteAttributeAction $deleteAction;

    public function __construct(
        CreateAttributeAction $createAction,
        UpdateAttributeAction $updateAction,
        DeleteAttributeAction $deleteAction
    ) {
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->authorizeResource(Attribute::class, 'attribute');
    }

    public function index(): Renderable
    {
        return view('attribute::index', ['attributes' => $this->createAction->repository->findAll()]);
    }

    public function create(): Renderable
    {
        return view('attribute::create', ['attribute' => new Attribute]);
    }

    public function store(Store $request): RedirectResponse
    {
        $dto = AttributeDTO::fromRequest($request);
        $this->createAction->execute($dto);
        return redirect()->route('attributes.index');
    }

    public function edit(Attribute $attribute): Renderable
    {
        return view('attribute::edit', ['attribute' => $this->createAction->repository->findById($attribute->id)]);
    }

    public function update(Update $request, Attribute $attribute): RedirectResponse
    {
        $dto = AttributeDTO::fromRequest($request)->withId($attribute->id);
        $this->updateAction->execute($dto);
        return redirect()->route('attributes.index');
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $this->deleteAction->execute($attribute->id);
        return redirect()->route('attributes.index');
    }
}
