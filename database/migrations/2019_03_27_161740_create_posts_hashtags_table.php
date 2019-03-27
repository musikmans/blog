<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsHashtagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts_hashtags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('post_id')->unsigned();
            $table->bigInteger('hashtag_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('posts_hashtags', function($table) {
            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('hashtag_id')->references('id')->on('hashtags');
        });

        DB::update("ALTER TABLE posts_hashtags AUTO_INCREMENT = 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts_hasgtags');
    }
}