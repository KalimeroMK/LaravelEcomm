<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->longText('description')->index();
            $table->text('short_des')->index();
            $table->string('logo')->index();
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->string('site-name');
            $table->string('keywords')->nullable();
            $table->string('google-site-verification')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
