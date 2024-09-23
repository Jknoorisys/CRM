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
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); 
            $table->boolean('login_access')->default(false); // Login Access: Yes/No (true/false)
            
            // Permissions stored as JSON fields
            $table->longText('contact_permissions')->nullable(); // Multiple contact permissions (create, view_self, view_all)
            $table->longText('lead_permissions')->nullable();    // Multiple lead permissions (create, view_self, view_all)
            $table->longText('activity_permissions')->nullable(); // Multiple activity permissions (create, view_self, view_all)
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_groups');
    }
};
