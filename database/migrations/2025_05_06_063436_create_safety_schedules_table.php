<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSafetySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('safety_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('master_shops')->onDelete('cascade');
            $table->date('scheduled_date');
            $table->string('status')->default('Planned'); // Optional: Planned, In Progress, Done
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('safety_schedules');
    }
}
