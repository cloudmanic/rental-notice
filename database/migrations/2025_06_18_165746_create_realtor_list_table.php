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
        Schema::create('realtor_list', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('csv_id')->nullable();
            $table->string('email')->nullable();
            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('office_name')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('county')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('mobile')->nullable();
            $table->string('license_type')->nullable();
            $table->string('license_number')->nullable();
            $table->date('original_issue_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('association')->nullable();
            $table->string('agency')->nullable();
            $table->integer('listings')->nullable();
            $table->decimal('listings_volume', 15, 2)->nullable();
            $table->integer('sold')->nullable();
            $table->decimal('sold_volume', 15, 2)->nullable();
            $table->string('email_status')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('license_number');
            $table->index('state');
            $table->index('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realtor_list');
    }
};
