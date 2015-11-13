<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.

     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('width')->comment('宽度');
            $table->integer('height')->comment('高度');
            $table->unsignedInteger('file_id')->comment('原始文件外键');
            $table->unsignedInteger('thumbnail_file_id')->comment('缩略图文件外键');
            $table->unsignedInteger('high_resolution_file_id')->comment('高分辨率文件外键');
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
        Schema::drop('images');
    }
}
