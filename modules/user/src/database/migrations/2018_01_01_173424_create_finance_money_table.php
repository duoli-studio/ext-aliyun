<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateFinanceMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_money', function (Blueprint $table) {
            $table->increments('id')->comment('id');
            $table->decimal('amount', 10, 2)->default(0.00)->comment('金额');
            $table->integer('account_id')->comment('账户id');
            $table->integer('editor_id')->comment('操作人id');
            $table->string('type', 45)->default('none')->comment('类型 充值charge、支付pay、提现cash、退款refund、收入earn、保证金支出bondpay、保证金退还bondrefund、手续费fee');
            $table->string('order_no', 45)->comment('订单号');
            $table->string('flow_no', 45)->comment('资金流水号');
            $table->string('note', 255)->comment('备注');
            $table->dateTime('created_at')->nullable()->comment('创建日期');
            $table->dateTime('updated_at')->nullable()->comment('修改日期');

            

            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_money');
    }
}