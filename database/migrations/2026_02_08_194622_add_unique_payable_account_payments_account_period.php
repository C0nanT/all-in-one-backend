<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payable_account_payments', function (Blueprint $table) {
            $table->dropIndex(['payable_account_id', 'period']);
            $table->unique(['payable_account_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payable_account_payments', function (Blueprint $table) {
            $table->dropUnique(['payable_account_id', 'period']);
            $table->index(['payable_account_id', 'period']);
        });
    }
};
