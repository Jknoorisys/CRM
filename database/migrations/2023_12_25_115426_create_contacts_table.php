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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source')->comment('Source')->constrained('sources')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('fname');
            $table->string('lname');
            $table->string('mobile_number', 20)->comment('Mobile Number');
            $table->string('phone_number', 20)->comment('Phone Number');
            $table->foreignId('designation')->comment('Designation')->constrained('designations')->onDelete('cascade');
            $table->string('company');
            $table->string('website');
            $table->string('linkedin');
            $table->foreignId('country')->comment('Country')->constrained('countries')->onDelete('cascade');
            $table->foreignId('city')->comment('City')->constrained('cities')->onDelete('cascade');
            $table->foreignId('referred_by')->comment('Referred By')->constrained('referred_by')->onDelete('cascade');
            $table->string('photo');
            $table->foreignId('status')->comment('Contact Status')->constrained('contact_status')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
