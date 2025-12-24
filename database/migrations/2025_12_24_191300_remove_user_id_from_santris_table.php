<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Removes user_id foreign key and column from santris table.
     * Santri no longer needs login credentials.
     * Adds NIS (Nomor Induk Santri) as unique identifier.
     */
    public function up(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['user_id']);
            
            // Drop the user_id column
            $table->dropColumn('user_id');
            
            // Add NIS (Nomor Induk Santri) as unique identifier
            $table->string('nis')->nullable()->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            // Remove NIS column
            $table->dropColumn('nis');
            
            // Re-add user_id column with foreign key
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
