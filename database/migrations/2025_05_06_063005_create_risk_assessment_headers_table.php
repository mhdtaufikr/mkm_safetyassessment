<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiskAssessmentHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risk_assessment_headers', function (Blueprint $table) {
            $table->id();
            $table->string('scope_number')->nullable(); // Scope (Number)
            $table->text('finding_problem');
            $table->text('potential_hazards')->nullable();
            $table->unsignedTinyInteger('severity')->nullable();
            $table->unsignedTinyInteger('possibility')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->string('risk_level')->nullable();
            $table->text('risk_reduction_proposal')->nullable();
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
        Schema::dropIfExists('risk_assessment_headers');
    }
}
