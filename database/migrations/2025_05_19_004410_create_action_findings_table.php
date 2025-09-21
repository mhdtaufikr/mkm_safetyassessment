<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('action_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_assessment_detail_id')->constrained('risk_assessment_details')->onDelete('cascade');
            $table->text('description'); // Deskripsi tindakan
            $table->string('pic')->nullable(); // Penanggung jawab
            $table->date('due_date')->nullable(); // Tanggal jatuh tempo
            $table->enum('status', ['Open', 'In Progress', 'Done'])->default('Open'); // Status progress
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_findings');
    }
};
