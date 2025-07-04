<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // فهارس مركبة للبحث السريع
        Schema::table('theses', function (Blueprint $table) {
            $table->index(['university_id', 'specialization_id', 'degree_id']);
            $table->index(['year', 'degree_id']);
            $table->index(['author_id', 'year']);
        });
        
        // فهارس للجداول المرجعية
        Schema::table('universities', function (Blueprint $table) {
            $table->index('name');
        });
        
        Schema::table('specializations', function (Blueprint $table) {
            $table->index('name');
        });
        
        Schema::table('authors', function (Blueprint $table) {
            $table->index('name');
        });
    }
    
    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropIndex(['university_id', 'specialization_id', 'degree_id']);
            $table->dropIndex(['year', 'degree_id']);
            $table->dropIndex(['author_id', 'year']);
        });
        
        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
        
        Schema::table('specializations', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
        
        Schema::table('authors', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
};