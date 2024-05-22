<?php

use App\Models\Comment;
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
        Schema::create('tweets', function (Blueprint $table) {
            $table->id();
            $table->longText('body');
            $table->string('likes_id')->nullable();
            $table->string('retweets_id')->nullable();
            $table->integer('receiver_id')->nullable();
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('tweets');
    }
};
