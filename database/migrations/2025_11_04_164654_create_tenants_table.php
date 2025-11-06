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
        // Schema::create('tenants', function (Blueprint $table) {
        //     $table->id();

        //     // Link tenant to a user
        //     $table->foreignId('user_id')
        //           ->constrained('users')
        //           ->onDelete('cascade');

        //     // Tenant house details
        //     $table->string('house_location');
        //     $table->string('house_title');
        //     $table->string('house_number');
        //     $table->decimal('rent', 10, 2);
        //     $table->decimal('utilities', 10, 2)->default(0.00);

        //     // Automatically calculated field (optional, if you want to store it)
        //     $table->decimal('total_rent', 10, 2)->storedAs('rent + utilities');

        //     $table->timestamps();
        // });

        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('house_id')->nullable()->constrained('houses')->onDelete('set null');
            $table->foreignId('landlord_id')->nullable()->constrained('users')->onDelete('set null'); // If landlords are users too

            $table->string('national_id')->nullable();

            // Rental details
            $table->decimal('rent', 10, 2);
            $table->decimal('utilities', 10, 2)->default(0.00);
            $table->decimal('security_deposit', 10, 2)->default(0.00);
            $table->decimal('total_rent', 10, 2)->storedAs('rent + utilities');
            $table->enum('payment_status', ['paid', 'pending', 'overdue'])->default('pending');

            // Lease details
            $table->date('lease_start_date')->nullable();
            $table->date('lease_end_date')->nullable();
            $table->enum('lease_status', ['active', 'expired', 'terminated'])->default('active');
            $table->date('payment_due_date')->nullable();

            // Contacts
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            // System tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->date('moved_out_at')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
