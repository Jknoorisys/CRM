<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); 
            $table->string('email'); 
            $table->string('mobile_no')->nullable(); 
            $table->text('message')->nullable(); 
            $table->text('inquiry_source')->nullable();
            $table->string('inquiry_for')->nullable();
            $table->string('no_of_resources')->nullable();
            $table->string('time_period')->nullable();
            $table->string('tech_stack')->nullable();
            $table->string('emp_experience')->nullable();
            $table->enum('status', ['new', 'add_to_lead'])->default('new');
            $table->bigInteger('add_to_lead_by')->comment('add to lead by')->nullable();
            $table->softDeletes();
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }
 
    
   
 
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiries');
    }
};
