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
        Schema::table('notices', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['tenant_id']);

            // Then drop the column
            $table->dropColumn('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            // Add the column back
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
