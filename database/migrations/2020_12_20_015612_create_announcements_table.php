<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('credit_paid');
            $table->date('publish_at');
            $table->boolean('has_content');
            $table->text('content')->nullable();
            $table->text('image')->nullable();
            $table->string('status', 100);
            $table->text('remark')->nullable();
            $table->string('audience', 10);
            $table->bigInteger('merchant_id')->nullable();
            $table->bigInteger('requested_by')->nullable();
            $table->bigInteger('actioned_by')->nullable();
            $table->timestamps();

            $table->index('title');
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
        Schema::dropIfExists('announcements');
    }
}
