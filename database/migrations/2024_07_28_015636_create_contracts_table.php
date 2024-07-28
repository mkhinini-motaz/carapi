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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('daily_price');
            $table->unsignedInteger('starts_at');
            $table->unsignedInteger('ends_at');

            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')
                ->onDelete('cascade')
                ->references('id')
                ->on('clients');

            $table->unsignedBigInteger('car_id');
            $table->foreign('car_id')
                ->onDelete('cascade')
                ->references('id')
                ->on('cars');

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
        Schema::dropIfExists('contracts');
    }
};
