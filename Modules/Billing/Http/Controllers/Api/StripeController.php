<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers\Api;

use Modules\Billing\Actions\Stripe\CreateStripeChargeAction;
use Modules\Billing\DTOs\StripeDTO;
use Modules\Billing\Http\Requests\Api\Stripe as StripeData;
use Modules\Core\Http\Controllers\Api\CoreController;
use Stripe\Exception\ApiErrorException;

class StripeController extends CoreController
{
    private CreateStripeChargeAction $createAction;

    public function __construct(CreateStripeChargeAction $createAction)
    {
        $this->createAction = $createAction;
    }

    /**
     * success response method.
     *
     *
     * @throws ApiErrorException
     */
    public function stripe(StripeData $request): void
    {
        $dto = StripeDTO::fromRequest($request);
        $this->createAction->execute($dto);
    }
}
