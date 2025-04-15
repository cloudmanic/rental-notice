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
            $table->text('draft_pdf')->nullable()->after('error_message');
            $table->text('final_pdf')->nullable()->after('draft_pdf');
            $table->text('certificate_pdf')->nullable()->after('final_pdf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn(['draft_pdf', 'final_pdf', 'certificate_pdf']);
        });
    }
};
