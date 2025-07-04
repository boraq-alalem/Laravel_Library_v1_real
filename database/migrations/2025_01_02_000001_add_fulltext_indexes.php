<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->fullText(['title']);
        });
        
        Schema::table('authors', function (Blueprint $table) {
            $table->fullText(['name']);
        });
        
        Schema::table('reserved_thesis_titles', function (Blueprint $table) {
            $table->fullText(['title', 'person_name']);
        });
    }
    
    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropFullText(['title']);
        });
        
        Schema::table('authors', function (Blueprint $table) {
            $table->dropFullText(['name']);
        });
        
        Schema::table('reserved_thesis_titles', function (Blueprint $table) {
            $table->dropFullText(['title', 'person_name']);
        });
    }
};