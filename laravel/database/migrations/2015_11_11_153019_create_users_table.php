<?php

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
            $table->increments('id');
            $table->string('username')->unique()->nullable()->default(null)->comment('用户名');
            $table->string('email')->unique()->nullable()->default(null)->comment('Email');
            $table->string('phone')->unique()->nullable()->default(null)->comment('电话');
            $table->string('password', 60)->comment('密码');
            $table->boolean('is_baned')->default(false)->comment('是否禁用');
            $table->rememberToken();
            $table->softDeletes();
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
        Schema::drop('users');
    }
}
