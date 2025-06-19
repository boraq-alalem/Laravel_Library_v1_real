<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reserved_thesis_titles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 1024);
            $table->string('person_name', 255);
            $table->string('university', 255);
            $table->string('specialization', 255);
            $table->string('degree', 255);
            $table->string('date', 255);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reserved_thesis_titles');
    }
};
