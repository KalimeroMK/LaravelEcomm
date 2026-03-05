<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            // Customer info
            $table->string('first_name')->nullable()->after('user_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('email')->nullable()->after('last_name');
            $table->string('phone')->nullable()->after('email');
            
            // Address fields
            $table->string('country')->nullable()->after('phone');
            $table->string('city')->nullable()->after('country');
            $table->string('state')->nullable()->after('city');
            $table->string('address1')->nullable()->after('state');
            $table->string('address2')->nullable()->after('address1');
            $table->string('post_code', 20)->nullable()->after('address2');
            
            // Add index for searching
            $table->index(['email', 'phone']);
            $table->index(['country', 'city']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'first_name',
                'last_name',
                'email',
                'phone',
                'country',
                'city',
                'state',
                'address1',
                'address2',
                'post_code',
            ]);
        });
    }
};
