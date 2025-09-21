use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('risk_assessment_details', function (Blueprint $table) {
            $table->text('finding_problem')->nullable()->after('header_id');
            $table->integer('severity')->nullable()->after('finding_problem');
            $table->integer('likelihood')->nullable()->after('severity');
            $table->integer('risk_level')->nullable()->after('likelihood');
        });
    }

    public function down()
    {
        Schema::table('risk_assessment_details', function (Blueprint $table) {
            $table->dropColumn(['finding_problem', 'severity', 'likelihood', 'risk_level']);
        });
    }
};
