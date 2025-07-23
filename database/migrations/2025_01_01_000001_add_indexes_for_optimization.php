<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // فهارس جدول الرسائل - حسب أولوية البحث
        Schema::table('theses', function (Blueprint $table) {
            // فهرس العنوان (الأهم) - محدود بـ 255 حرف
            DB::statement('ALTER TABLE theses ADD INDEX idx_title_search (title(255))');
            // فهرس النص الكامل للعنوان
            $table->fullText('title', 'idx_title_fulltext');
            
            // فهارس مركبة للجامعة والتخصص (مرونة في البحث)
            $table->index('university_id', 'idx_university');
            $table->index(['university_id', 'specialization_id'], 'idx_uni_spec');
            // الفهرس الشامل موجود مسبقاً
        });
        
        // فهارس العناوين المحجوزة
        Schema::table('reserved_thesis_titles', function (Blueprint $table) {
            DB::statement('ALTER TABLE reserved_thesis_titles ADD INDEX idx_reserved_title (title(255))');
            $table->index('person_name', 'idx_person_name');
            $table->fullText(['title', 'person_name'], 'idx_reserved_fulltext');
        });
        
        // فهارس الجداول المرجعية
        Schema::table('universities', function (Blueprint $table) {
            $table->index('name', 'idx_university_name');
        });
        
        Schema::table('specializations', function (Blueprint $table) {
            $table->index('name', 'idx_specialization_name');
        });
        
        Schema::table('authors', function (Blueprint $table) {
            $table->index('name', 'idx_author_name');
            $table->fullText('name', 'idx_author_fulltext');
        });
    }
    
    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            DB::statement('ALTER TABLE theses DROP INDEX idx_title_search');
            $table->dropFullText('idx_title_fulltext');
            $table->dropIndex('idx_university');
            $table->dropIndex('idx_uni_spec');
        });
        
        Schema::table('reserved_thesis_titles', function (Blueprint $table) {
            DB::statement('ALTER TABLE reserved_thesis_titles DROP INDEX idx_reserved_title');
            $table->dropIndex('idx_person_name');
            $table->dropFullText('idx_reserved_fulltext');
        });
        
        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex('idx_university_name');
        });
        
        Schema::table('specializations', function (Blueprint $table) {
            $table->dropIndex('idx_specialization_name');
        });
        
        Schema::table('authors', function (Blueprint $table) {
            $table->dropIndex('idx_author_name');
            $table->dropFullText('idx_author_fulltext');
        });
    }
};