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


        Schema::create('machines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_code_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->integer('machine_type_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('area_id')->nullable();
            $table->date('buy_date')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('warranty_upto_date')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
        });

        Schema::create('machines_location_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('machine_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->integer('previous_area_id')->nullable();
            $table->integer('current_area_id')->nullable();
            $table->integer('previous_department_id')->nullable();
            $table->integer('current_department_id')->nullable();
            $table->date('change_date')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
        Schema::dropIfExists('machines_location_history');

    }
};
