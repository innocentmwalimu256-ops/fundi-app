<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Subscription Plans Table
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Starter, Professional, Premium
            $table->string('slug')->unique();
            $table->decimal('price', 12, 2)->default(0); // TZS
            $table->string('currency', 10)->default('TZS');
            $table->integer('duration_days')->default(30);
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->integer('request_limit')->default(50); // limit per month, 0 = unlimited
            $table->integer('portfolio_limit')->default(5); // 0 = unlimited
            $table->integer('service_area_limit')->default(1);
            $table->boolean('contact_access')->default(true);
            $table->boolean('priority_listing')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->string('analytics_level')->default('basic'); // basic, advanced
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // 2. Technician Subscriptions Table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->string('status')->default('active'); // free_trial, active, expired, cancelled, suspended
            $table->dateTime('started_at');
            $table->dateTime('expires_at');
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();
        });

        // 3. Subscription Payments Table
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('TZS');
            $table->string('payment_reference')->unique();
            $table->string('payment_method')->default('mobile_money'); // mpesa, tigopesa, airtelmoney, card, manual
            $table->string('status')->default('success'); // pending, under_review, verified, success, failed, rejected
            $table->text('admin_notes')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        // 4. Enhance Service Requests with connection fee, contact unlock tracking and payment note
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'contact_unlocked')) {
                $table->boolean('contact_unlocked')->default(false)->after('status');
            }
            if (!Schema::hasColumn('service_requests', 'service_cost_agreed')) {
                $table->decimal('service_cost_agreed', 12, 2)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('service_requests', 'connection_fee')) {
                $table->decimal('connection_fee', 12, 2)->default(2000)->after('payment_status');
            }
            if (!Schema::hasColumn('service_requests', 'connection_fee_status')) {
                $table->string('connection_fee_status')->default('paid')->after('connection_fee');
            }
            if (!Schema::hasColumn('service_requests', 'connection_fee_reference')) {
                $table->string('connection_fee_reference')->nullable()->after('connection_fee_status');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
