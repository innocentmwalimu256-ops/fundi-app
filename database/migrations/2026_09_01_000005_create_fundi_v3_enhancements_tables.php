<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Portfolios Table
        Schema::create('technician_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('project_date')->nullable();
            $table->timestamps();
        });

        // 2. Weekly Availability Schedule Table
        Schema::create('technician_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
            $table->string('day_of_week'); // Monday, Tuesday, etc.
            $table->time('start_time')->default('08:00:00');
            $table->time('end_time')->default('18:00:00');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // 3. Service Coverage Areas Table
        Schema::create('technician_service_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
            $table->string('area_name'); // Kinondoni, Ilala, Temeke, Ubungo, Kigamboni
            $table->timestamps();
        });

        // 4. Cancellation Records Table
        Schema::create('cancellation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->foreignId('cancelled_by')->constrained('users')->cascadeOnDelete();
            $table->string('reason'); // Found another technician, Schedule conflict, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 5. Complaint Evidences Table
        Schema::create('complaint_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_type')->default('image'); // image, document
            $table->timestamps();
        });

        // 6. User Reports Table
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reported_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('request_id')->nullable()->constrained('service_requests')->nullOnDelete();
            $table->string('reason'); // Fraud / suspicious activity, Misconduct, Poor behaviour, False information, Other
            $table->text('details')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, action_taken, dismissed
            $table->timestamps();
        });

        // 7. Update service_requests with payment_status
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status'); // unpaid, paid
        });

        // 8. Update technician_profiles with trust score indicators
        Schema::table('technician_profiles', function (Blueprint $table) {
            $table->unsignedInteger('completion_rate')->default(98)->after('completed_jobs_count');
            $table->unsignedInteger('response_rate')->default(95)->after('completion_rate');
            $table->string('avg_response_time')->default('15 min')->after('response_rate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reports');
        Schema::dropIfExists('complaint_evidences');
        Schema::dropIfExists('cancellation_records');
        Schema::dropIfExists('technician_service_areas');
        Schema::dropIfExists('technician_availabilities');
        Schema::dropIfExists('technician_portfolios');

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });

        Schema::table('technician_profiles', function (Blueprint $table) {
            $table->dropColumn(['completion_rate', 'response_rate', 'avg_response_time']);
        });
    }
};
