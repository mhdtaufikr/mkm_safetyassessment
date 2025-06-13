<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActionTakenToRiskAssessmentHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('risk_assessment_headers', function (Blueprint $table) {
        $table->boolean('is_followed_up')->default(false);
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
   public function down()
{
    Schema::table('risk_assessment_headers', function (Blueprint $table) {
        $table->dropColumn('is_followed_up');
    });
}
}
