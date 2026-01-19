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
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('email');
            $table->string('company_logo')->nullable()->after('company_name');
            $table->string('company_phone')->nullable()->after('company_logo');
            $table->string('company_email')->nullable()->after('company_phone');
            $table->string('company_address', 500)->nullable()->after('company_email');
            $table->string('company_website')->nullable()->after('company_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_logo',
                'company_phone',
                'company_email',
                'company_address',
                'company_website'
            ]);
        });
    }
};
