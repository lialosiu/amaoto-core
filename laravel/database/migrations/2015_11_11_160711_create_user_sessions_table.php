<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('session_id')->comment('SessionID');
            $table->unsignedInteger('user_id')->nullable()->comment('用户外键');
            $table->timestamp('login_time')->nullable()->comment('登录时间');
            $table->string('login_ip')->nullable()->comment('登录IP');
            $table->string('login_ua')->nullable()->comment('登录UA');
            $table->timestamp('last_active_time')->nullable()->comment('最后活动时间');
            $table->string('last_active_ip')->nullable()->comment('最后活动IP');
            $table->string('last_active_ua')->nullable()->comment('最后活动UA');
            $table->string('last_active_full_url')->nullable()->comment('最后活动URL');
            $table->boolean('is_locked')->default(false)->comment('是否已锁定');
            $table->boolean('is_expired')->default(false)->comment('是否已过期');
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
        Schema::drop('user_sessions');
    }
}
