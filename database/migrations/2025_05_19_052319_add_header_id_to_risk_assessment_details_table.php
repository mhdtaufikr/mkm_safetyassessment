public function up()
{
    Schema::table('risk_assessment_details', function (Blueprint $table) {
        $table->unsignedBigInteger('header_id')->after('id');

        // Foreign key optional (recommended)
        $table->foreign('header_id')->references('id')->on('risk_assessment_headers')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('risk_assessment_details', function (Blueprint $table) {
        $table->dropForeign(['header_id']);
        $table->dropColumn('header_id');
    });
}
