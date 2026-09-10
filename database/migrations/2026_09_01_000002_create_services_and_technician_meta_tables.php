<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('wrench');
            $table->enum('status', ['active', 'disabled'])->default('active');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('technician_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['technician_id', 'service_id']);
        });

        Schema::create('technician_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
            $table->string('document_type'); // id_card, vocational_cert, business_license, other
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('technician_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('professional_title');
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->integer('years_experience')->default(1);
            $table->string('location');
            $table->string('service_area')->nullable();
            $table->text('bio')->nullable();
            $table->json('skills')->nullable();
            $table->string('id_document_path')->nullable();
            $table->string('certificate_document_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_applications');
        Schema::dropIfExists('technician_documents');
        Schema::dropIfExists('technician_services');
        Schema::dropIfExists('services');
    }
};
