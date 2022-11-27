<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->float('sub_total');
            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->foreign('shipping_id')->references('id')->on('shipping')->onDelete('SET NULL');
            $table->float('total_amount');
            $table->integer('quantity');
            $table->enum('payment_method', ['cod', 'paypal', 'stripe'])->default('cod');
            $table->enum('payment_status', ['paid', 'unpaid'])->default('paid');
            $table->enum('status', ['new', 'process', 'delivered', 'cancel'])->default('new');
            $table->string('payer_id')->nullable();
            $table->string('transaction_reference')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
