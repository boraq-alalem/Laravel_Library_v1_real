<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // إضافة الفهارس المتبقية فقط
        Schema::table('theses', function (Blueprint $table) {
            // فهارس مركبة للبحث السريع
            $table->index(['author_id', 'university_id'], 'idx_author_uni');
            $table->index(['specialization_id', 'degree_id'], 'idx_spec_degree');
            $table->index(['degree_id', 'year'], 'idx_degree_year_new');
            $table->index(['author_id', 'year'], 'idx_author_year_new');
            $table->index(['university_id', 'specialization_id'], 'idx_uni_spec_new');
        });
        
        // فهارس للجداول المرجعية (إذا لم تكن موجودة)
        Schema::table('authors', function (Blueprint $table) {
            $table->index('name', 'idx_author_name_new');
        });

        Schema::table('universities', function (Blueprint $table) {
            $table->index('name', 'idx_university_name_new');
        });

        Schema::table('specializations', function (Blueprint $table) {
            $table->index('name', 'idx_specialization_name_new');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->index('created_at', 'idx_users_created');
        });
    }
    
    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropIndex('idx_author_uni');
            $table->dropIndex('idx_spec_degree');
            $table->dropIndex('idx_degree_year_new');
            $table->dropIndex('idx_author_year_new');
            $table->dropIndex('idx_uni_spec_new');
        });
        
        Schema::table('authors', function (Blueprint $table) {
            $table->dropIndex('idx_author_name_new');
        });

        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex('idx_university_name_new');
        });

        Schema::table('specializations', function (Blueprint $table) {
            $table->dropIndex('idx_specialization_name_new');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_created');
        });
    }
};