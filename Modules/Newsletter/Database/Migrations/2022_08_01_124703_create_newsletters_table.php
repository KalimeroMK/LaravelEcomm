<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('newsletters');
    }
};