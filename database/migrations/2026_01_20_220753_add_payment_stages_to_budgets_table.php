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
        Schema::table('budgets', function (Blueprint $table) {
            $table->integer('payment_stage_1')->default(0)->after('total');
            $table->integer('payment_stage_2')->default(0)->after('payment_stage_1');
            $table->integer('payment_stage_3')->default(0)->after('payment_stage_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn(['payment_stage_1', 'payment_stage_2', 'payment_stage_3']);
        });
    }
};
