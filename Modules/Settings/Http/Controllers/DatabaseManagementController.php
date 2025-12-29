<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class DatabaseManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\AdminMiddleware::class);
    }

    /**
     * Show database management page
     */
    public function index(): View|Factory|Application
    {
        return view('settings::database.index');
    }

    /**
     * Run migrations
     */
    public function migrate(): RedirectResponse
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return redirect()->back()->with('success', 'Migrations run successfully. '.$output);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Migration failed: '.$e->getMessage());
        }
    }

    /**
     * Run fresh migrations
     */
    public function migrateFresh(): RedirectResponse
    {
        try {
            Artisan::call('migrate:fresh', ['--force' => true]);
            $output = Artisan::output();

            return redirect()->back()->with('success', 'Fresh migrations run successfully. '.$output);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Fresh migration failed: '.$e->getMessage());
        }
    }

    /**
     * Run seeders
     */
    public function seed(): RedirectResponse
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            $output = Artisan::output();

            return redirect()->back()->with('success', 'Seeders run successfully. '.$output);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Seeding failed: '.$e->getMessage());
        }
    }

    /**
     * Run fresh migrations with seeders
     */
    public function migrateFreshSeed(): RedirectResponse
    {
        try {
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            $output = Artisan::output();

            return redirect()->back()->with('success', 'Fresh migrations with seeders run successfully. '.$output);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Fresh migration with seeders failed: '.$e->getMessage());
        }
    }
}
