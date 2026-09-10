<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique(); // e.g. REQ-2026-000125
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->text('description');
            $table->string('location');
            $table->date('preferred_date');
            $table->time('preferred_time')->nullable();
            $table->enum('urgency', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', [
                'requested',
                'pending',
                'accepted',
                'declined',
                'cancelled',
                'quotation_pending',
                'quotation_accepted',
                'scheduled',
                'on_the_way',
                'in_progress',
                'completed',
                'client_confirmed',
                'reviewed'
            ])->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('request_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->timestamps();
        });

        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->decimal('labour_cost', 12, 2)->default(0);
            $table->decimal('materials_cost', 12, 2)->default(0);
            $table->decimal('transport_cost', 12, 2)->default(0);
            $table->decimal('other_cost', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2);
            $table->text('notes')->nullable();
            $table->string('estimated_duration')->nullable(); // e.g. "3 Hours"
            $table->date('valid_until')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('service_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->enum('status', [
                'scheduled',
                'on_the_way',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('scheduled');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_jobs');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('request_images');
        Schema::dropIfExists('service_requests');
    }
};
