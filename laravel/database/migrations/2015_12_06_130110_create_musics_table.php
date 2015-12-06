<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMusicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable()->index()->comment('歌名');
            $table->string('artist')->nullable()->index()->comment('艺术家');
            $table->string('year')->nullable()->index()->comment('年份');
            $table->integer('track')->nullable()->comment('音轨');
            $table->string('genre')->nullable()->index()->comment('流派');
            $table->double('playtime')->nullable()->comment('播放时长');
            $table->double('bitrate')->nullable()->comment('码流');
            $table->text('tags')->nullable()->comment('标签数据');
            $table->unsignedInteger('file_id')->comment('文件外键');
            $table->unsignedInteger('cover_image_id')->nullable()->comment('封面图片外键');
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
        Schema::drop('musics');
    }
}
