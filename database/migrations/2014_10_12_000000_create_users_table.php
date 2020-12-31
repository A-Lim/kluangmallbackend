<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('avatar')->nullable();
            $table->string('member_no')->unique();
            $table->string('gender', 6)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('otp', 6)->nullable();
            $table->string('otp_token')->nullable();
            $table->dateTime('otp_expiry')->nullable();
            $table->string('device_token')->nullable();
            $table->string('status', 20);
            $table->rememberToken();
            $table->timestamps();

            $table->index('name');
            $table->index('phone');
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
        Schema::dropIfExists('users');
    }
}
