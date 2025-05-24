<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('notice_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 8, 2);
            $table->decimal('past_due_rent', 8, 2);
            $table->decimal('late_charges', 8, 2);

            // Other charges
            for ($i = 1; $i <= 5; $i++) {
                $table->string("other_{$i}_title")->nullable();
                $table->decimal("other_{$i}_price", 8, 2)->nullable();
            }

            // Flags
            $table->boolean('payment_other_means')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
