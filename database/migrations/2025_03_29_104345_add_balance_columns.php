<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0);
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('balance');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('balance');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
};