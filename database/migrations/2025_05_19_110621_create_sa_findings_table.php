use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sa_findings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_assessment');
            $table->text('countermeasure');
            $table->string('pic_area')->nullable();
            $table->string('pic_repair')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('Open');
            $table->timestamps();

            $table->foreign('id_assessment')->references('id')->on('risk_assessment_headers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sa_findings');
    }
};
