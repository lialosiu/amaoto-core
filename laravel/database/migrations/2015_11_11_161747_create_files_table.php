<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('文件名');
            $table->string('ext')->comment('后缀名');
            $table->string('mime')->comment('MIME');
            $table->unsignedInteger('user_id')->comment('上传用户外键');
            $table->unsignedInteger('base_file_id')->comment('基础文件外键');
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
        Schema::drop('files');
    }
}
