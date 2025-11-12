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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // bigIncrements unsigned bigint

            // Foreign keys using bigInteger IDs like invoice table
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();

            $table->decimal('amount', 12, 2)->default(0)->index();
            $table->char('currency', 3)->default('KES');
            $table->string('payment_method')->index();
            $table->string('status')->default('pending')->index();

            $table->string('gateway')->nullable();
            $table->string('gateway_transaction_id')->nullable()->unique();
            $table->string('merchant_reference')->nullable()->index();

            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();

            $table->decimal('fees_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->nullable();

            $table->date('settlement_date')->nullable();
            $table->integer('attempts')->default(0);

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->string('idempotency_key')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};


