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
        if (Schema::hasColumn('notices', 'include_all_other_occupents')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->dropColumn('include_all_other_occupents');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->boolean('include_all_other_occupents')->default(false);
        });
    }
};
