<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create faculties table
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->timestamps();
        });

        // Create departments table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->timestamps();
        });

        // Create admin users table
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['super_admin', 'admin', 'viewer'])->default('viewer');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // Create quiz responses table - main table for storing responses
        Schema::create('quiz_responses', function (Blueprint $table) {
            $table->id();
            
            // Personal Identity Data (Section I)
            $table->year('student_year');
            $table->foreignId('faculty_id')->constrained();
            $table->foreignId('department_id')->constrained();
            $table->string('nim', 50)->unique();
            $table->string('full_name');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('phone', 20);
            $table->text('address');
            $table->enum('living_arrangement', [
                'Kos', 
                'Rumah orang tua', 
                'Rumah keluarga', 
                'Asrama', 
                'Kontrak'
            ]);
            $table->string('origin_province');
            $table->enum('origin_area_type', [
                'perkotaan', 
                'pedesaan', 
                'pinggiran kota', 
                'daerah terpencil', 
                'daerah industri'
            ]);
            $table->string('email')->nullable();
            $table->string('religion');
            $table->enum('parents_marital_status', [
                'menikah', 
                'cerai hidup', 
                'cerai mati', 
                'pisah tidak resmi', 
                'menikah lagi'
            ]);
            $table->integer('child_order');
            $table->integer('siblings_count');
            $table->string('scholarship')->nullable();
            $table->string('admission_path');
            $table->enum('parents_education', [
                'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'
            ]);
            $table->enum('parents_income', [
                '<2000000', 
                '2000000-5000000', 
                '5000000-10000000', 
                '>10000000'
            ]);
            $table->integer('family_members_count');
            
            // Medical History Questions (22-28)
            $table->boolean('has_chronic_disease')->default(false);
            $table->text('chronic_disease_details')->nullable();
            $table->boolean('current_medication')->default(false);
            $table->text('medication_details')->nullable();
            $table->boolean('head_injury_history')->default(false);
            $table->text('injury_details')->nullable();
            $table->enum('substance_use', ['Tidak Pernah', 'Pernah', 'Masih aktif']);
            $table->text('substance_details')->nullable();
            $table->boolean('psychological_treatment_history')->default(false);
            $table->text('treatment_details')->nullable();
            $table->boolean('family_mental_health_history')->default(false);
            $table->text('family_history_details')->nullable();
            $table->text('family_relationship_description')->nullable();
            
            // Quiz Progress Tracking
            $table->enum('quiz_status', [
                'started', 
                'phq9_completed', 
                'dass21_completed', 
                'completed'
            ])->default('started');
            
            // Store responses as JSON for flexibility
            $table->json('phq9_responses')->nullable(); // Questions 1-9
            $table->json('dass21_responses')->nullable(); // Questions 1-30
            
            // Calculated Scores
            $table->integer('phq9_total_score')->nullable();
            $table->string('phq9_category')->nullable();
            $table->integer('dass21_total_score')->nullable();
            $table->string('dass21_category')->nullable();
            
            // Risk Assessment
            $table->enum('overall_risk_level', [
                'Low', 'Moderate', 'High', 'Critical'
            ])->nullable();
            
            // Completion tracking
            $table->boolean('phq9_passed_threshold')->default(false);
            $table->boolean('needs_dass21')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('phq9_completed_at')->nullable();
            $table->timestamp('dass21_completed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['quiz_status']);
            $table->index(['phq9_category', 'dass21_category']);
            $table->index(['overall_risk_level']);
            $table->index(['completed_at']);
            $table->index(['faculty_id', 'department_id']);
            $table->index(['student_year']);
        });

        // Create quiz sessions table for tracking incomplete sessions
        Schema::create('quiz_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_token', 100)->unique();
            $table->foreignId('quiz_response_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('current_step')->default(0); // 0=identity, 1=phq9, 2=dass21
            $table->json('temp_data')->nullable(); // Store incomplete form data
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['session_token']);
            $table->index(['expires_at']);
        });

        // Create system logs for admin actions
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['admin_user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_logs');
        Schema::dropIfExists('quiz_sessions');
        Schema::dropIfExists('quiz_responses');
        Schema::dropIfExists('admin_users');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
    }
};