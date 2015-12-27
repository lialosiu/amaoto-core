<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable()->index()->comment('歌名');
            $table->string('artist')->nullable()->index()->comment('艺术家');
            $table->string('year')->nullable()->index()->comment('年份');
            $table->string('genre')->nullable()->index()->comment('流派');
            $table->unsignedInteger('cover_image_id')->nullable()->comment('封面图片外键');
            $table->unsignedInteger('user_id')->nullable()->comment('创建用户外键');
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
        Schema::drop('albums');
    }
}
