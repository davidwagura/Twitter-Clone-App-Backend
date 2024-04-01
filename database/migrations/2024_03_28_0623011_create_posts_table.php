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
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('content_id');
            $table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');

            $table->unsignedBigInteger('like_id')->nullable();
            $table->foreign('like_id')->references('id')->on('likes')->onDelete('cascade');

            $table->unsignedBigInteger('comment_id')->nullable();
            $table->timestamps();

            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');




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
