<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('archive_theses', function (Blueprint $table) {
            $table->id();
            $table->string('title', 1024);
            $table->string('year')->index();
            $table->string('pdf_path')->nullable();
            $table->foreignId('university_id')->constrained('universities')->onDelete('restrict');
            $table->foreignId('specialization_id')->constrained('specializations')->onDelete('restrict');
            $table->foreignId('degree_id')->constrained('degrees')->onDelete('restrict');
            $table->foreignId('author_id')->constrained('authors')->onDelete('restrict');
            $table->timestamps();
            $table->index(['university_id', 'specialization_id', 'degree_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('archive_theses');
    }
};
