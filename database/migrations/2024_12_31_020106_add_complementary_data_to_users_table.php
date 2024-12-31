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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 15)->nullable()->after('remember_token');
            $table->string('mobile', 15)->nullable()->after('phone');
            $table->longText('picture')->nullable()->after('mobile');
            $table->boolean('is_active')->default(false)->after('mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'mobile', 'picture', 'is_active']);
        });
    }
};
