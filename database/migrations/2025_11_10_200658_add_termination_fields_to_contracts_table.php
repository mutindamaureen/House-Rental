<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->enum('termination_status', ['pending', 'partial', 'completed'])->nullable()->after('status');
            $table->timestamp('termination_initiated_at')->nullable()->after('termination_status');
            $table->string('termination_initiated_by')->nullable()->after('termination_initiated_at'); // 'admin', 'tenant', 'landlord'
            $table->string('landlord_termination_signature')->nullable()->after('termination_initiated_by');
            $table->string('tenant_termination_signature')->nullable()->after('landlord_termination_signature');
            $table->timestamp('landlord_signed_termination_at')->nullable()->after('tenant_termination_signature');
            $table->timestamp('tenant_signed_termination_at')->nullable()->after('landlord_signed_termination_at');
            $table->timestamp('terminated_at')->nullable()->after('tenant_signed_termination_at');
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'termination_status',
                'termination_initiated_at',
                'termination_initiated_by',
                'landlord_termination_signature',
                'tenant_termination_signature',
                'landlord_signed_termination_at',
                'tenant_signed_termination_at',
                'terminated_at'
            ]);
        });
    }
};
