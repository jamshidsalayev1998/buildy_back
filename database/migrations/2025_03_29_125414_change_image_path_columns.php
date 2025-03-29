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
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('image_path');
            $table->string('image')->nullable();
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('image_path');
            $table->string('image')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('image_path');
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('image_path')->nullable(false)->change();
            $table->dropColumn('image');
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->string('image_path')->nullable(false)->change();
            $table->dropColumn('image');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('image_path')->nullable(false)->change();
            $table->dropColumn('image');
        });
    }
};
