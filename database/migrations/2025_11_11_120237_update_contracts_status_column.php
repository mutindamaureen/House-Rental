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
        Schema::table('contracts', function (Blueprint $table) {
            // Change the enum to include 'terminated'
            $table->enum('status', ['pending', 'signed', 'terminated'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Revert back to original enum
            $table->enum('status', ['pending', 'signed'])
                  ->default('pending')
                  ->change();
        });
    }
};
