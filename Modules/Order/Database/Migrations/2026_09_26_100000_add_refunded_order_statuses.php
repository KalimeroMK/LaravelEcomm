<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // sqlite (tests) stores enums as TEXT with a CHECK constraint that
        // Laravel does not add for enum(), so only MySQL/MariaDB needs the ALTER.
        if (DB::getDriverName() !== 'mysql' && DB::getDriverName() !== 'mariadb') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','processing','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('pending','paid','unpaid','refunded') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql' && DB::getDriverName() !== 'mariadb') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('pending','paid','unpaid') NOT NULL DEFAULT 'pending'");
    }
};
