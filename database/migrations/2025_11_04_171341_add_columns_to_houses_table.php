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
        Schema::table('houses', function (Blueprint $table) {
            // Add a foreign key to the landlords table
            $table->foreignId('landlord_id')
                ->nullable()
                ->constrained('landlords')
                ->onDelete('set null')
                ->after('id'); // You can change position if you like

            // Add a house status column after total_rent
            $table->enum('status', ['available', 'occupied', 'under_maintenance'])
                ->default('available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            // Drop the foreign key and column safely
            $table->dropForeign(['landlord_id']);
            $table->dropColumn('landlord_id');

            // Drop the status column
            $table->dropColumn('status');
        });
    }
};
