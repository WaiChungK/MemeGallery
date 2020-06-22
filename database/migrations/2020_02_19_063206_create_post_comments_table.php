<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('post_id')->index();
            $table->string('message', 2000);
            $table->unsignedInteger('child_of')->nullable(); // The parent comment's id. If null means this is the parent
            $table->timestamps();
			$table->foreign('user_id')->references('users')->on('id');
			$table->foreign('post_id')->references('posts')->on('id');
			$table->foreign('child_of')->references('post_comments')->on('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_comments');
    }
}
