<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();

            $table->text('comment');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('replied_comment')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('post_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};
