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
        // Schema::table('tweets', function (Blueprint $table) {
        //     $table->dropColumn('receiver_id');
        // });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down(): void
    {
        // Schema::table('tweets', function (Blueprint $table) {
        //     $table->integer('receiver_id')->nullable();
        // });
    }
};
