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
            $table->bigInteger('source')->comment('Source')->nullable();
            $table->string('email')->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('mobile_number', 20)->comment('Mobile Number')->nullable();
            $table->string('phone_number', 20)->comment('Phone Number')->nullable();
            $table->bigInteger('designation')->comment('Designation')->nullable();
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->bigInteger('country')->comment('Country')->nullable();
            $table->bigInteger('city')->comment('City')->nullable();
            $table->bigInteger('referred_by')->comment('Referred By')->nullable();
            $table->string('photo')->nullable();
            $table->bigInteger('status')->comment('Contact Status')->nullable();
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
