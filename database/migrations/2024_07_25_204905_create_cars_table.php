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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
     
            // TODO: color

            $table->unsignedInteger('daily_price');
            $table->string('licence_plate')->unique();
            $table->string('serial_number')->unique();
            $table->string('manufacturer');
            $table->string('model_name');
            $table->unsignedSmallInteger('model_year');
            $table->unsignedSmallInteger('fiscal_horse_power');
            $table->unsignedSmallInteger('number_of_cylinders');
            $table->unsignedSmallInteger('transmission_type');
            $table->unsignedSmallInteger('fuel_type');
            $table->string('first_circulation_date');
            $table->unsignedSmallInteger('type');
            $table->unsignedInteger('circulation_tax_ends_at');
            $table->unsignedInteger('insurance_ends_at');
            $table->unsignedInteger('technical_control_ends_at');

            $table->unsignedBigInteger('agency_id');
            $table->foreign('agency_id')
                ->onDelete('cascade')
                ->references('id')
                ->on('agencies');

            $table->unsignedInteger('created_at');
            $table->unsignedInteger('updated_at');
            $table->unsignedInteger('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
