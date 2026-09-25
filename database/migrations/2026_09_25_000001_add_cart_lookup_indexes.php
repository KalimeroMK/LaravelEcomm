<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes for the cart lookups that run on every page (header cart/wishlist)
 * and on every cart write (abandoned-cart tracking).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table): void {
            if (! Schema::hasIndex('carts', 'carts_session_id_index')) {
                $table->index('session_id');
            }
            if (! Schema::hasIndex('carts', 'carts_user_id_order_id_index')) {
                $table->index(['user_id', 'order_id']);
            }
        });

        Schema::table('abandoned_carts', function (Blueprint $table): void {
            if (! Schema::hasIndex('abandoned_carts', 'abandoned_carts_user_id_converted_index')) {
                $table->index(['user_id', 'converted']);
            }
            if (! Schema::hasIndex('abandoned_carts', 'abandoned_carts_session_id_converted_index')) {
                $table->index(['session_id', 'converted']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table): void {
            $table->dropIndex('carts_session_id_index');
            $table->dropIndex('carts_user_id_order_id_index');
        });

        Schema::table('abandoned_carts', function (Blueprint $table): void {
            $table->dropIndex('abandoned_carts_user_id_converted_index');
            $table->dropIndex('abandoned_carts_session_id_converted_index');
        });
    }
};
