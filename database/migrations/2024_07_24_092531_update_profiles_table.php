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
        Schema::table('profiles', function (Blueprint $table) {
            // Rename the column from 'image_path' to 'profile_img'
            $table->renameColumn('image_path', 'profile_img');

            // Add a new column 'background_img'
            $table->string('background_img')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Rename the column back from 'profile_img' to 'image_path'
            $table->renameColumn('profile_img', 'image_path');

            // Drop the 'background_img' column
            $table->dropColumn('background_img');
        });
    }
};
