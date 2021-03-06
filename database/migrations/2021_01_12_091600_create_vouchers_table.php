<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('merchant_id')->unsigned()->nullable();
            $table->string('type', 20)->index();
            $table->boolean('display_to_all')->default(false);
            $table->string('status', 100);
            $table->text('image')->nullable();
            $table->string('name', 100);
            $table->text('description');
            $table->integer('points');
            $table->integer('free_points')->nullable();
            $table->text('data')->nullable();
            $table->text('qr')->nullable();
            $table->boolean('has_redemption_limit')->default(false);
            $table->date('fromDate')->nullable();
            $table->date('toDate')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->bigInteger('created_by')->unsigned();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('merchant_id')
                  ->references('id')
                  ->on('merchants')
                  ->onDelete('cascade');

            $table->index('name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
}
