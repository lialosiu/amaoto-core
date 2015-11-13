<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nick_name')->comment('用户昵称');
            $table->enum('sex', ['undefined', 'male', 'female'])->default('undefined')->comment('性别');
            $table->date('birthday')->nullable()->default(null)->comment('生日');
            $table->string('qq')->nullable()->default(null)->comment('QQ');
            $table->string('profile')->nullable()->default(null)->comment('个性签名');
            $table->unsignedInteger('user_id')->comment('用户外键');
            $table->unsignedInteger('avatar_id')->nullable()->default(null)->comment('头像外键');
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
        Schema::drop('user_infos');
    }
}