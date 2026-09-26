<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Currency;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_currency_formats_amounts(): void
    {
        $this->assertEquals('$100.00', currency(100));
        $this->assertEquals('USD', active_currency()->code);
    }

    public function test_visitor_can_switch_display_currency(): void
    {
        $response = $this->get('/currency/EUR');

        $response->assertRedirect();
        $this->assertEquals('EUR', session('currency'));

        // 100 USD * 0.92 = 92.00 EUR, left-positioned symbol
        $this->assertEquals('€92.00', currency(100));
    }

    public function test_right_positioned_currency_with_no_decimals(): void
    {
        session(['currency' => 'MKD']);

        // 100 * 56.60 = 5,660 with the symbol on the right
        $this->assertEquals('5,660 ден', currency(100));
    }

    public function test_unknown_currency_code_returns_404_and_keeps_session(): void
    {
        $this->get('/currency/XXX')->assertNotFound();
        $this->assertNull(session('currency'));
    }

    public function test_inactive_currency_cannot_be_selected(): void
    {
        Currency::where('code', 'GBP')->update(['is_active' => false]);

        $this->get('/currency/GBP')->assertNotFound();
    }
}
