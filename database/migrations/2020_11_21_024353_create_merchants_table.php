<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('merchant_category_id')->unsigned();
            $table->string('name');
            $table->text('logo')->nullable();
            $table->string('status', 20);
            $table->string('floor', 2);
            $table->string('unit', 10);
            $table->string('business_reg_no')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('description')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->bigInteger('created_by')->unsigned();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('merchant_category_id')
                  ->references('id')
                  ->on('merchant_categories')
                  ->onDelete('cascade');

            $table->index('name');
            $table->index('business_reg_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('merchants');
        Schema::enableForeignKeyConstraints();
    }
}
