<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToRiskAssessmentHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('risk_assessment_headers', function (Blueprint $table) {
        $table->string('status')->default('Open')->after('is_followed_up');
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
        $table->dropColumn('status');
    });
    }
}
