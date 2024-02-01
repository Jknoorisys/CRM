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
            $table->string('title')->comment('Activity Title');
            $table->string('lead_id')->comment('Lead');
            $table->bigInteger('user_id')->comment('Action Performed By')->nullable();
            $table->bigInteger('medium')->comment('Activity Medium')->nullable();
            $table->bigInteger('stage')->comment('Lead Stage')->nullable();
            $table->text('summary');
            $table->string('attachment');
            $table->timestamp('follow_up_date');
            $table->enum('is_action_performed', ['yes', 'no'])->default('no');
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
