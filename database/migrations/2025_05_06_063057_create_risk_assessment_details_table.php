<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiskAssessmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risk_assessment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_assessment_header_id')->constrained()->onDelete('cascade');
            $table->text('finding_problem');
            $table->text('potential_hazards')->nullable();
            $table->text('countermeasure')->nullable();
            $table->date('genba_date')->nullable();
            $table->string('shop')->nullable();

            // Responsibility
            $table->string('pic_area')->nullable();
            $table->string('pic_repair')->nullable();
            $table->date('due_date')->nullable();

            // Progress
            $table->string('status')->nullable();
            $table->date('progress_date')->nullable();
            $table->string('checked_by')->nullable();
            $table->string('code')->nullable();

            $table->string('file')->nullable(); // path to attachment
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
        Schema::dropIfExists('risk_assessment_details');
    }
}
