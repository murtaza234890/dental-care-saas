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
            $table->foreignId('clinic_id')->nullable()->after('id')->constrained('clinics')->nullOnDelete();
            $table->string('phone', 30)->nullable()->after('email');
            $table->enum('role', ['platform_owner', 'clinic_admin', 'dentist', 'receptionist'])
                ->default('clinic_admin')
                ->after('phone');
            $table->boolean('is_active')->default(true)->after('role');

            $table->index(['clinic_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn(['clinic_id', 'phone', 'role', 'is_active']);
        });
    }
};
