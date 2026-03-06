<?php

declare(strict_types=1);

namespace Modules\GeoLocalization\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\GeoLocalization\Services\CurrencyService;
use Modules\GeoLocalization\Services\GeoIpService;

class GeoLocalizationController extends CoreController
{
    public function __construct(
        private readonly GeoIpService $geoIpService,
        private readonly CurrencyService $currencyService,
    ) {}

    /**
     * Get current location based on IP
     */
    public function currentLocation(Request $request): JsonResponse
    {
        // Check for manual override
        if ($request->has('ip')) {
            $ip = $request->get('ip');
        } else {
            $ip = $this->geoIpService->getClientIp();
        }

        $location = $this->geoIpService->locate($ip);

        return response()->json([
            'success' => true,
            'data' => $location->toArray(),
        ]);
    }

    /**
     * Detect location from specific IP
     */
    public function detectIp(string $ip): JsonResponse
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid IP address.',
            ], 400);
        }

        $location = $this->geoIpService->locate($ip);

        return response()->json([
            'success' => true,
            'data' => $location->toArray(),
        ]);
    }

    /**
     * Get current session currency
     */
    public function currentCurrency(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'currency' => $this->currencyService->getCurrentCurrency(),
                'symbol' => $this->currencyService->getCurrencySymbol($this->currencyService->getCurrentCurrency()),
            ],
        ]);
    }

    /**
     * Set session currency
     */
    public function setCurrency(Request $request): JsonResponse
    {
        $request->validate([
            'currency' => 'required|string|size:3',
        ]);

        $currency = strtoupper($request->get('currency'));
        $this->currencyService->setCurrency($currency);

        return response()->json([
            'success' => true,
            'message' => 'Currency updated successfully.',
            'data' => [
                'currency' => $currency,
                'symbol' => $this->currencyService->getCurrencySymbol($currency),
            ],
        ]);
    }

    /**
     * Get available currencies
     */
    public function availableCurrencies(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->currencyService->getAvailableCurrencies(),
        ]);
    }

    /**
     * Get exchange rates
     */
    public function exchangeRates(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'base' => config('geolocalization.base_currency', 'USD'),
                'rates' => $this->currencyService->getRates(),
                'updated_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Convert currency
     */
    public function convert(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);

        $amount = (float) $request->get('amount');
        $from = strtoupper($request->get('from'));
        $to = strtoupper($request->get('to'));

        $converted = $this->currencyService->convert($amount, $from, $to);

        return response()->json([
            'success' => true,
            'data' => [
                'original_amount' => $amount,
                'original_currency' => $from,
                'converted_amount' => round($converted, 2),
                'target_currency' => $to,
                'formatted' => $this->currencyService->format($converted, $to),
            ],
        ]);
    }

    /**
     * Get all localization data for current user
     */
    public function all(Request $request): JsonResponse
    {
        $ip = $this->geoIpService->getClientIp();
        $location = $this->geoIpService->locate($ip);
        $currency = $this->currencyService->getCurrentCurrency();

        return response()->json([
            'success' => true,
            'data' => [
                'location' => $location->toArray(),
                'currency' => [
                    'code' => $currency,
                    'symbol' => $this->currencyService->getCurrencySymbol($currency),
                ],
                'locale' => app()->getLocale(),
                'timezone' => $location->timezone ?? config('app.timezone'),
            ],
        ]);
    }
}
