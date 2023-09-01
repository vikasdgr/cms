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

        Schema::create('areas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->integer('department_id')->nullable();
            $table->string('area_user_id')->nullable();
            $table->string('person_contact_no')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
          });

          Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->string('person_contact_no')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
          });
          Schema::create('brands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
          });
          Schema::create('machine_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_no', 100)->nullable();
            $table->integer('machine_type_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
          });
          Schema::create('machine_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description',500)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
          });

          Schema::create('problems', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description',500)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
          });

          Schema::create('maintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description',500)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
          });

          Schema::create('resolving_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description',500)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
          });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('areas');
         Schema::dropIfExists('departments');
         Schema::dropIfExists('brands');
         Schema::dropIfExists('machine_models');
         Schema::dropIfExists('machine_types');
         Schema::dropIfExists('problems');
         Schema::dropIfExists('maintenances');
         Schema::dropIfExists('resolving_actions');
    }
};
