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
        Schema::table('stages', function (Blueprint $table) {
            $table->string('lead_category')->comment('lead category type')->nullable()->after('id'); // Adjust the type and attributes as needed
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropColumn('lead_category');
        });
    }
};
