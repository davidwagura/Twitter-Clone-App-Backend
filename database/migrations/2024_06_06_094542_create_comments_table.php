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

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('body');
            $table->string('image_path')->nullable();
            $table->string('likes_id')->nullable();
            $table->string('retweets_id')->nullable();
            $table->unsignedBigInteger('tweet_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->integer('likes')->default(0);
            $table->integer('retweets')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('comments');
    }
};
