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
            $table->foreignId('contact')->comment('Contact')->constrained('contacts')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->foreignId('stage')->comment('Lead Stage')->constrained('lead_stages')->onDelete('cascade');
            $table->foreignId('source')->comment('Lead Source')->constrained('lead_sources')->onDelete('cascade');
            $table->foreignId('type')->comment('Lead Type')->constrained('lead_types')->onDelete('cascade');
            $table->foreignId('assigned_to')->comment('Assigned To')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->comment('Created By')->constrained('users')->onDelete('cascade');
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
