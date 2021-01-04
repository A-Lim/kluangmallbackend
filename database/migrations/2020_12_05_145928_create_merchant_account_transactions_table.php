<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_account_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('merchant_id')->unsigned();
            $table->bigInteger('credit');
            $table->string('title');
            $table->string('type', 100);
            $table->text('remark')->nullable();
            $table->boolean('refunded')->default(false);
            $table->bigInteger('refund_transaction_id')->nullable();
            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();

            $table->foreign('merchant_id')
                  ->references('id')
                  ->on('merchants')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_account_transactions');
    }
}
