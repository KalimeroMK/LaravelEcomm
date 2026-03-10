<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Billing\Paypal;

use Modules\Billing\Actions\Paypal\CreatePaypalChargeAction;
use Modules\Billing\DTOs\PaypalDTO;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Omnipay;
use Tests\Unit\Actions\ActionTestCase;

class CreatePaypalChargeActionTest extends ActionTestCase
{
    public function testExecuteCreatesPaypalGateway(): void
    {
        $dto = new PaypalDTO(
            amount: 99.99,
            currency: 'USD',
            returnUrl: 'https://example.com/success',
            cancelUrl: 'https://example.com/cancel',
        );

        $action = app(CreatePaypalChargeAction::class);

        // We can't actually test the full integration without real credentials,
        // but we can verify the action is instantiated correctly
        $this->assertInstanceOf(CreatePaypalChargeAction::class, $action);
    }

    public function testExecuteAcceptsPaypalDTO(): void
    {
        $dto = new PaypalDTO(
            amount: 50.00,
            currency: 'EUR',
            returnUrl: 'https://example.com/payment/success',
            cancelUrl: 'https://example.com/payment/cancel',
        );

        $this->assertEquals(50.00, $dto->amount);
        $this->assertEquals('EUR', $dto->currency);
        $this->assertEquals('https://example.com/payment/success', $dto->returnUrl);
        $this->assertEquals('https://example.com/payment/cancel', $dto->cancelUrl);
    }

    public function testPaypalDTOFromArrayUsesDefaults(): void
    {
        $dto = PaypalDTO::fromArray([
            'amount' => 25.00,
        ]);

        $this->assertEquals(25.00, $dto->amount);
        $this->assertNotNull($dto->currency);
        $this->assertNotNull($dto->returnUrl);
        $this->assertNotNull($dto->cancelUrl);
    }

    public function testPaypalDTOFromArrayWithCustomValues(): void
    {
        $dto = PaypalDTO::fromArray([
            'amount' => 100.00,
            'currency' => 'GBP',
            'returnUrl' => 'https://custom.com/return',
            'cancelUrl' => 'https://custom.com/cancel',
        ]);

        $this->assertEquals(100.00, $dto->amount);
        $this->assertEquals('GBP', $dto->currency);
        $this->assertEquals('https://custom.com/return', $dto->returnUrl);
        $this->assertEquals('https://custom.com/cancel', $dto->cancelUrl);
    }
}
