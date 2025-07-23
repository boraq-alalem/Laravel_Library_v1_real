<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // فهارس محسنة للرسائل
        Schema::table('theses', function (Blueprint $table) {
            // فهرس مركب للبحث السريع
            $table->index(['degree_id', 'specialization_id', 'university_id'], 'idx_theses_filters');
            // فهرس للترتيب
            $table->index(['id', 'created_at'], 'idx_theses_latest');
            // فهرس للسنة
            $table->index('year', 'idx_theses_year');
        });
        
        // فهرس نصي للعناوين
        DB::statement('ALTER TABLE theses ADD FULLTEXT(title)');
        
        // فهارس للجداول المرتبطة
        Schema::table('authors', function (Blueprint $table) {
            $table->index('name', 'idx_authors_name');
        });
        
        Schema::table('universities', function (Blueprint $table) {
            $table->index('name', 'idx_universities_name');
        });
        
        Schema::table('specializations', function (Blueprint $table) {
            $table->index('name', 'idx_specializations_name');
        });
    }

    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropIndex('idx_theses_filters');
            $table->dropIndex('idx_theses_latest');
            $table->dropIndex('idx_theses_year');
        });
        
        DB::statement('ALTER TABLE theses DROP INDEX title');
        
        Schema::table('authors', function (Blueprint $table) {
            $table->dropIndex('idx_authors_name');
        });
        
        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex('idx_universities_name');
        });
        
        Schema::table('specializations', function (Blueprint $table) {
            $table->dropIndex('idx_specializations_name');
        });
    }
};