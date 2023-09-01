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
        Schema::create('cases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('case_no')->nullable();
            $table->integer('machine_id')->nullable();
            $table->date('open_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->string('work_types')->nullable();  // Maintenanace // Installation // BreakDown
            $table->string('work_order_types')->nullable();   //Repair //General Service //Installation
            $table->string('status',1)->nullable(); //P -pending/ R- Progress- C-closed / Follwup
            $table->string('description',1000)->nullable();
            $table->string('generated_user_id')->nullable();
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
        Schema::dropIfExists('cases');
    }
};
