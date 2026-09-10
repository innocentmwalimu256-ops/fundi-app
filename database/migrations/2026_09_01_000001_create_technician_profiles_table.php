<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('professional_title')->nullable();
            $table->text('bio')->nullable();
            $table->integer('years_experience')->default(1);
            $table->string('location')->nullable();
            $table->string('service_area')->nullable();
            $table->enum('availability_status', ['available', 'busy', 'offline'])->default('available');
            $table->enum('verification_status', ['unapplied', 'pending', 'approved', 'rejected', 'suspended'])->default('unapplied');
            $table->text('rejection_reason')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->integer('completed_jobs_count')->default(0);
            $table->json('skills')->nullable();
            $table->json('working_hours')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_profiles');
    }
};
