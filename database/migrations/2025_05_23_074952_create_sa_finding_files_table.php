use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sa_finding_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sa_finding_id');
            $table->string('file'); // nama file atau path
            $table->timestamps();

            // Foreign key ke sa_findings
            $table->foreign('sa_finding_id')->references('id')->on('sa_findings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sa_finding_files');
    }
};
