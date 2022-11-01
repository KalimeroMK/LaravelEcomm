<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::table('conditions', function (Blueprint $table) {
            //
        });
    }
};