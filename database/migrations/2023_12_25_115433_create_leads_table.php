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
        Schema::create('leads', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->bigInteger('contact')->comment('Contact')->nullable();
            $table->string('title');
            $table->text('description');
            $table->bigInteger('stage')->comment('Lead Stage')->nullable();
            $table->bigInteger('source')->comment('Lead Source')->nullable();
            $table->bigInteger('type')->comment('Lead Type')->nullable();
            $table->bigInteger('assigned_to')->comment('Assigned To')->nullable();
            $table->bigInteger('created_by')->comment('Created By')->nullable();
            $table->timestamp('last_contacted_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
