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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('lead_id')->comment('Lead');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreignId('medium')->comment('Activity Medium')->constrained('activity_medium')->onDelete('cascade');
            $table->text('summary');
            $table->string('attachment');
            $table->datetime('follow_up_date');
            $table->foreignId('stage')->comment('Stage')->constrained('stages')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
