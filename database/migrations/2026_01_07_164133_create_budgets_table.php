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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->constrained()->onDelete('cascade');
            $table->unsignedInteger("client_id")->constrained()->onDelete('cascade');
            $table->integer("budget_number");
            $table->date("issue_date");
            $table->date("due_date");
            $table->enum("status", ['draft', 'sent', 'accepted', 'rejected']);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax', 15, 2);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
