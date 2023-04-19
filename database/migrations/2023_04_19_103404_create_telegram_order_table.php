<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelegramOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_order', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->string('no')->default('')->comment('订单编号');
            $table->string('u_money')->default('0.00')->comment('入账金额');
            $table->unsignedInteger('payment_time')->comment('入账时间');
            $table->string('order_hash')->default('')->comment('入账hash');
            $table->string('order_address')->default('')->comment('入账地址');
            $table->dateTime('order_createtime');
            $table->dateTime('order_updatetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telegram_order');
    }
}
