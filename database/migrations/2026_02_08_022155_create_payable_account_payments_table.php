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
        Schema::create('payable_account_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payable_account_id')->constrained('payable_accounts')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->foreignId('payer_id')->constrained('users')->restrictOnDelete();
            $table->date('period');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['payable_account_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payable_account_payments');
    }
};
