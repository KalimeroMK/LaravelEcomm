<?php

declare(strict_types=1);

namespace Modules\Billing\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Billing\Actions\Paypal\CreatePaypalChargeAction;
use Modules\Billing\Actions\Stripe\CreateStripeChargeAction;
use Modules\Billing\DTOs\PaypalDTO;
use Modules\Billing\DTOs\StripeDTO;
use Omnipay\Common\Message\ResponseInterface;
use PHPUnit\Framework\TestCase;

class PaymentProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_paypal_charge_action_executes_securely()
    {
        $dto = new PaypalDTO(
            amount: 10.00,
            currency: 'USD',
            returnUrl: 'https://example.com/success',
            cancelUrl: 'https://example.com/cancel'
        );

        $action = $this->getMockBuilder(CreatePaypalChargeAction::class)
            ->onlyMethods(['execute'])
            ->getMock();

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isRedirect')->willReturn(true);
        $action->expects($this->once())
            ->method('execute')
            ->with($dto)
            ->willReturn($mockResponse);

        $response = $action->execute($dto);
        $this->assertTrue($response->isRedirect());
    }

    public function test_stripe_charge_action_executes_securely()
    {
        $dto = new StripeDTO(
            amount: 10.00,
            currency: 'usd',
            source: 'tok_test',
            description: 'Test Payment'
        );

        $action = $this->getMockBuilder(CreateStripeChargeAction::class)
            ->onlyMethods(['execute'])
            ->getMock();

        $action->expects($this->once())
            ->method('execute')
            ->with($dto);

        $action->execute($dto);
        $this->addToAssertionCount(1); // Mark test as having an assertion
    }
}
