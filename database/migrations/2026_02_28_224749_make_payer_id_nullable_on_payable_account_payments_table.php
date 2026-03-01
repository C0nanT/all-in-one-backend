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
        Schema::table('payable_account_payments', function (Blueprint $table) {
            $table->dropForeign(['payer_id']);
            $table->unsignedBigInteger('payer_id')->nullable()->change();
            $table->foreign('payer_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payable_account_payments', function (Blueprint $table) {
            $table->dropForeign(['payer_id']);
            $table->unsignedBigInteger('payer_id')->nullable(false)->change();
            $table->foreign('payer_id')->references('id')->on('users')->restrictOnDelete();
        });
    }
};
