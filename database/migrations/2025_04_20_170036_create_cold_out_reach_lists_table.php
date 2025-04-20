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
        Schema::create('cold_out_reach_lists', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Property Manager', 'Real Estate Agent'])->default('Property Manager')->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('street_1')->nullable();
            $table->string('street_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip', 10)->nullable();
            $table->date('expiration')->nullable();
            $table->string('license_number')->nullable();
            $table->string('status')->nullable();
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('domain')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cold_out_reach_lists');
    }
};
