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
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('service_no')->nullable();
            $table->integer('case_id')->nullable();
            $table->integer('parent_service_id')->nullable();
            $table->date('service_date')->nullable();
            $table->string('service_time')->nullable();
            $table->string('remarks',1500)->nullable();
            $table->string('technician_name')->nullable();
            $table->string('service_type',1)->nullable();  // M,I,R
            $table->string('status',1)->default('O')->nullable();                    //C -closed - F-Followup Required O-Open
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
        });


        Schema::create('service_captured_problems', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('case_id')->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('problem_id')->nullable();
            $table->string('is_remarks',1)->default('N')->nullable();
            $table->string('remarks',1500)->nullable();
            $table->string('problem_status',1)->default('O')->nullable();                    // O // Resolved
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('service_resolving_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('case_id')->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('resolving_action_id')->nullable();
            $table->string('is_remarks',1)->default('N')->nullable();
            $table->string('remarks',1500)->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('service_maintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('case_id')->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('maintenance_id')->nullable();
            $table->string('is_remarks',1)->default('N')->nullable();
            $table->string('remarks',1500)->nullable();
            $table->string('problem_status',1)->default('O')->nullable();                    // O // Resolved
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_captured_problems');
        Schema::dropIfExists('service_resolving_actions');
        Schema::dropIfExists('service_maintenances');
    }
};
