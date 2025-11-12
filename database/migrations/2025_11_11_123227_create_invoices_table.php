
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); // bigIncrements unsigned bigint
            $table->string('reference')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('house_id')->nullable()->constrained('houses')->nullOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->char('currency', 3)->default('KES');
            $table->text('description')->nullable();
            $table->enum('status', ['unpaid','partial','paid','overdue','cancelled'])->default('unpaid');
            $table->date('due_date')->nullable();
            $table->date('issued_date')->nullable();
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}



