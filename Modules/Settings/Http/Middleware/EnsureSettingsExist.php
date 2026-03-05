<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Settings\Actions\CreateDefaultSettingsAction;
use Modules\Settings\Actions\GetSettingsAction;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure settings always exist.
 * If no settings found, creates default ones automatically.
 */
readonly class EnsureSettingsExist
{
    public function __construct(
        private GetSettingsAction $getSettingsAction,
        private CreateDefaultSettingsAction $createDefaultSettingsAction
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Check if settings exist, if not create default ones
        $settings = $this->getSettingsAction->execute();

        if (empty($settings)) {
            $this->createDefaultSettingsAction->execute();
        }

        return $next($request);
    }
}
