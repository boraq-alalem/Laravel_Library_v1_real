<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reserved_thesis_titles', function (Blueprint $table) {
            $table->index('person_name', 'idx_reserved_person');
        });
    }
    
    public function down()
    {
        Schema::table('reserved_thesis_titles', function (Blueprint $table) {
            $table->dropIndex('idx_reserved_person');
        });
    }
};