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
        if (! Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->decimal('price_monthly', 10, 2)->default(0.00);
                $table->decimal('price_annual', 10, 2)->default(0.00);
                $table->string('currency', 10)->default('PKR');
                $table->integer('max_doctors')->default(1);
                $table->integer('max_staff')->default(2);
                $table->integer('max_patients')->default(200);
                $table->json('features')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
                $table->foreignId('plan_id')->constrained('subscription_plans')->restrictOnDelete();
                $table->string('status')->default('trialing');
                $table->string('billing_interval')->default('monthly');
                $table->datetime('trial_ends_at')->nullable();
                $table->datetime('current_period_start')->nullable();
                $table->datetime('current_period_end')->nullable();
                $table->datetime('cancelled_at')->nullable();
                $table->string('payment_method')->nullable();
                $table->timestamps();

                $table->index(['clinic_id', 'status']);
            });
        }

        if (! Schema::hasTable('platform_invoices')) {
            Schema::create('platform_invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
                $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
                $table->string('invoice_number')->unique();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 10)->default('PKR');
                $table->string('status')->default('paid');
                $table->date('billing_period_start')->nullable();
                $table->date('billing_period_end')->nullable();
                $table->datetime('paid_at')->nullable();
                $table->string('payment_reference')->nullable();
                $table->timestamps();

                $table->index(['clinic_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
