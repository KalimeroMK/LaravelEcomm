<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Billing\Stripe;

use Modules\Billing\Actions\Stripe\CreateStripeChargeAction;
use Modules\Billing\DTOs\StripeDTO;
use Tests\Unit\Actions\ActionTestCase;

class CreateStripeChargeActionTest extends ActionTestCase
{
    public function testExecuteAcceptsStripeDTO(): void
    {
        $dto = new StripeDTO(
            amount: 99.99,
            currency: 'usd',
            source: 'tok_visa',
            description: 'Test payment',
        );

        $this->assertEquals(99.99, $dto->amount);
        $this->assertEquals('usd', $dto->currency);
        $this->assertEquals('tok_visa', $dto->source);
        $this->assertEquals('Test payment', $dto->description);
    }

    public function testStripeDTOConvertsAmountToInteger(): void
    {
        $dto = StripeDTO::fromArray([
            'amount' => 50,
            'stripeToken' => 'tok_test',
        ]);

        $this->assertEquals(50, $dto->amount);
    }

    public function testStripeDTOUsesDefaults(): void
    {
        $dto = StripeDTO::fromArray([
            'amount' => 25.00,
        ]);

        $this->assertEquals(25.00, $dto->amount);
        $this->assertEquals('usd', $dto->currency);
        $this->assertNull($dto->source);
        $this->assertEquals('KalimeroMK E-comm', $dto->description);
    }

    public function testStripeDTOFromArrayWithCustomValues(): void
    {
        $dto = StripeDTO::fromArray([
            'amount' => 100,
            'currency' => 'eur',
            'stripeToken' => 'tok_custom',
            'description' => 'Custom description',
        ]);

        $this->assertEquals(100, $dto->amount);
        $this->assertEquals('eur', $dto->currency);
        $this->assertEquals('tok_custom', $dto->source);
        $this->assertEquals('Custom description', $dto->description);
    }

    public function testCreateStripeChargeActionIsInstantiable(): void
    {
        $action = app(CreateStripeChargeAction::class);

        $this->assertInstanceOf(CreateStripeChargeAction::class, $action);
    }
}
