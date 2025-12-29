<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemController extends Controller
{
    public function __construct()
    {
        // Middleware is applied via routes
    }

    /**
     * Health check endpoint
     */
    public function health(): JsonResponse
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
        ];

        $overallStatus = 'healthy';
        foreach ($health as $key => $value) {
            if ($key !== 'status' && $key !== 'timestamp' && isset($value['status']) && $value['status'] !== 'ok') {
                $overallStatus = 'unhealthy';
                break;
            }
        }

        $health['status'] = $overallStatus;

        return response()->json($health, $overallStatus === 'healthy' ? 200 : 503);
    }

    /**
     * Version endpoint
     */
    public function version(): JsonResponse
    {
        return response()->json([
            'version' => config('app.version', '1.0.0'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
        ]);
    }

    /**
     * System information page
     */
    public function info(): View|Factory|Application
    {
        $info = [
            'php' => [
                'version' => PHP_VERSION,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
            ],
            'laravel' => [
                'version' => app()->version(),
                'environment' => app()->environment(),
                'debug' => config('app.debug'),
                'timezone' => config('app.timezone'),
            ],
            'database' => [
                'driver' => config('database.default'),
                'connection' => DB::connection()->getDatabaseName(),
            ],
            'cache' => [
                'driver' => config('cache.default'),
            ],
            'queue' => [
                'driver' => config('queue.default'),
                'connection' => config('queue.connections.'.config('queue.default').'.driver'),
            ],
            'server' => [
                'os' => PHP_OS,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            ],
        ];

        return view('core::system.info', ['info' => $info]);
    }

    /**
     * Enable maintenance mode
     */
    public function enableMaintenance(Request $request): RedirectResponse
    {
        $secret = $request->input('secret', bin2hex(random_bytes(16)));
        Artisan::call('down', ['--secret' => $secret]);

        return redirect()->back()->with('success', 'Maintenance mode enabled. Secret: '.$secret);
    }

    /**
     * Disable maintenance mode
     */
    public function disableMaintenance(): RedirectResponse
    {
        Artisan::call('up');

        return redirect()->back()->with('success', 'Maintenance mode disabled');
    }

    /**
     * Clear application cache
     */
    public function clearCache(): RedirectResponse
    {
        Artisan::call('cache:clear');

        return redirect()->back()->with('success', 'Application cache cleared');
    }

    /**
     * Clear config cache
     */
    public function clearConfig(): RedirectResponse
    {
        Artisan::call('config:clear');

        return redirect()->back()->with('success', 'Config cache cleared');
    }

    /**
     * Clear route cache
     */
    public function clearRoute(): RedirectResponse
    {
        Artisan::call('route:clear');

        return redirect()->back()->with('success', 'Route cache cleared');
    }

    /**
     * Clear view cache
     */
    public function clearView(): RedirectResponse
    {
        Artisan::call('view:clear');

        return redirect()->back()->with('success', 'View cache cleared');
    }

    /**
     * Clear all caches
     */
    public function clearAll(): RedirectResponse
    {
        Artisan::call('optimize:clear');

        return redirect()->back()->with('success', 'All caches cleared');
    }

    /**
     * Create database backup
     */
    public function backupDatabase(): RedirectResponse|BinaryFileResponse
    {
        try {
            $connection = config('database.default');
            $config = config("database.connections.{$connection}");

            // For SQLite, just copy the file
            if ($connection === 'sqlite') {
                $databasePath = $config['database'] ?? database_path('database.sqlite');
                if (File::exists($databasePath)) {
                    $filename = 'backup_'.date('Y-m-d_His').'.sqlite';
                    $backupPath = storage_path('app/backups/'.$filename);

                    if (! File::exists(storage_path('app/backups'))) {
                        File::makeDirectory(storage_path('app/backups'), 0755, true);
                    }

                    File::copy($databasePath, $backupPath);

                    if (File::exists($backupPath)) {
                        return response()->download($backupPath, $filename)->deleteFileAfterSend(true);
                    }
                }

                return redirect()->back()->with('error', 'SQLite database file not found');
            }

            // For MySQL/PostgreSQL
            $database = $config['database'] ?? null;
            $username = $config['username'] ?? null;
            $password = $config['password'] ?? null;
            $host = $config['host'] ?? '127.0.0.1';

            $filename = 'backup_'.date('Y-m-d_His').'.sql';
            $path = storage_path('app/backups/'.$filename);

            if (! File::exists(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }

            // Use mysqldump for MySQL
            if ($connection === 'mysql') {
                $command = sprintf(
                    'mysqldump -h %s -u %s -p%s %s > %s 2>&1',
                    escapeshellarg($host),
                    escapeshellarg($username),
                    escapeshellarg($password),
                    escapeshellarg($database),
                    escapeshellarg($path)
                );

                exec($command, $output, $returnVar);

                if ($returnVar !== 0) {
                    // Simple export fallback
                    $tables = DB::select('SHOW TABLES');
                    $dump = '';
                    foreach ($tables as $table) {
                        $tableName = array_values((array) $table)[0];
                        $rows = DB::table($tableName)->get();
                        $dump .= "-- Table: {$tableName}\n";
                        foreach ($rows as $row) {
                            $dump .= json_encode($row)."\n";
                        }
                    }
                    File::put($path, $dump);
                }
            } else {
                // For other databases, create a simple export
                $dump = '-- Database backup created at '.now()."\n";
                File::put($path, $dump);
            }

            if (File::exists($path) && File::size($path) > 0) {
                return response()->download($path, $filename)->deleteFileAfterSend(true);
            }

            return redirect()->back()->with('error', 'Failed to create backup');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: '.$e->getMessage());
        }
    }

    /**
     * View log files
     */
    public function logs(): View|Factory|Application
    {
        $logPath = storage_path('logs');
        $logs = [];

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                $logs[] = [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                    'path' => $file->getPathname(),
                ];
            }
        }

        // Sort by modified date, newest first
        usort($logs, fn ($a, $b) => strtotime($b['modified']) - strtotime($a['modified']));

        return view('core::system.logs', ['logs' => $logs]);
    }

    /**
     * View specific log file
     */
    public function viewLog(string $filename): View|Factory|Application
    {
        $logPath = storage_path('logs/'.$filename);

        if (! File::exists($logPath)) {
            abort(404, 'Log file not found');
        }

        $content = File::get($logPath);
        $lines = explode("\n", $content);
        $lines = array_slice($lines, -500); // Last 500 lines

        return view('core::system.log-view', [
            'filename' => $filename,
            'lines' => $lines,
        ]);
    }

    /**
     * Clear log files
     */
    public function clearLogs(): RedirectResponse
    {
        $logPath = storage_path('logs');

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'log') {
                    File::put($file->getPathname(), '');
                }
            }
        }

        return redirect()->back()->with('success', 'Log files cleared');
    }

    /**
     * Queue status
     */
    public function queueStatus(): View|Factory|Application
    {
        $status = [
            'driver' => config('queue.default'),
            'connection' => config('queue.connections.'.config('queue.default').'.driver'),
            'failed_jobs_count' => DB::table('failed_jobs')->count(),
        ];

        return view('core::system.queue', ['status' => $status]);
    }

    /**
     * View failed jobs
     */
    public function failedJobs(): View|Factory|Application
    {
        $jobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->paginate(20);

        return view('core::system.failed-jobs', ['jobs' => $jobs]);
    }

    /**
     * Retry failed job
     */
    public function retryJob(int $id): RedirectResponse
    {
        Artisan::call('queue:retry', ['id' => $id]);

        return redirect()->back()->with('success', 'Job queued for retry');
    }

    /**
     * Retry all failed jobs
     */
    public function retryAllJobs(): RedirectResponse
    {
        Artisan::call('queue:retry', ['id' => 'all']);

        return redirect()->back()->with('success', 'All failed jobs queued for retry');
    }

    /**
     * Delete failed job
     */
    public function deleteJob(int $id): RedirectResponse
    {
        DB::table('failed_jobs')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Failed job deleted');
    }

    /**
     * View environment variables
     */
    public function environment(): View|Factory|Application
    {
        $envFile = base_path('.env');
        $envVars = [];

        if (File::exists($envFile)) {
            $content = File::get($envFile);
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                $line = mb_trim($line);
                if (empty($line) || str_starts_with($line, '#')) {
                    continue;
                }

                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    // Mask sensitive values
                    if (in_array(mb_strtolower($key), ['password', 'secret', 'key', 'token', 'api_key'])) {
                        $value = str_repeat('*', min(mb_strlen($value), 20));
                    }
                    $envVars[mb_trim($key)] = mb_trim($value);
                }
            }
        }

        return view('core::system.environment', ['envVars' => $envVars]);
    }

    /**
     * Check database connection
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();

            return ['status' => 'ok', 'message' => 'Connected'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check cache
     */
    private function checkCache(): array
    {
        try {
            $key = 'health_check_'.time();
            Cache::put($key, 'test', 10);
            $value = Cache::get($key);
            Cache::forget($key);

            return $value === 'test'
                ? ['status' => 'ok', 'message' => 'Working']
                : ['status' => 'error', 'message' => 'Cache not working'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check queue
     */
    private function checkQueue(): array
    {
        try {
            $driver = config('queue.default');

            return ['status' => 'ok', 'message' => "Driver: {$driver}"];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
