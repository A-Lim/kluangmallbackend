<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('myvoucher_id')->unsigned();
            $table->bigInteger('merchant_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('voucher_id')->unsigned();
            $table->string('type', 10);
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('voucher_id')
                  ->references('id')
                  ->on('vouchers')
                  ->onDelete('cascade');

            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_transactions');
    }
}
