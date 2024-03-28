<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ownerName');
            $table->string('content_id');
            $table->integer('like_id')->nullable();
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->timestamps();

            $table->foreign('ownerName')->references('id')->on('users');
            $table->foreign('content_id')->references('id')->on('contents');
            $table->foreign('likes_id')->references('id')->on('likes');
            $table->foreign('comment_id')->references('id')->on('comments');




        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
