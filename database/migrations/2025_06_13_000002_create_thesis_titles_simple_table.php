<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('thesis_titles_simple', function (Blueprint $table) {
            $table->id();
            $table->text('title')->unique();
            $table->string('person_name');
            $table->string('university');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('thesis_titles_simple');
    }
};
