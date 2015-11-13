<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBasicFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basic_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('md5')->comment('MD5');
            $table->string('sha1')->comment('SHA1');
            $table->unsignedInteger('size')->comment('文件大小');
            $table->string('disk')->comment('文件储存磁盘');
            $table->string('path')->comment('文件储存路径');
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
        Schema::drop('basic_files');
    }
}
