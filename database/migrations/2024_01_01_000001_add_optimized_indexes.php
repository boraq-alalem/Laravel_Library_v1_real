<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptimizedIndexes extends Migration
{
    public function up()
    {
        Schema::table('theses', function (Blueprint $table) {
            // فهارس محسنة للبحث السريع
            $table->index(['university_id', 'specialization_id', 'degree_id'], 'idx_uni_spec_deg');
            $table->index(['year', 'degree_id'], 'idx_year_degree');
            $table->index('created_at', 'idx_theses_created');
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->index('name', 'idx_authors_name');
        });

        Schema::table('universities', function (Blueprint $table) {
            $table->index('name', 'idx_universities_name');
        });

        Schema::table('specializations', function (Blueprint $table) {
            $table->index('name', 'idx_specializations_name');
        });

        Schema::table('degrees', function (Blueprint $table) {
            $table->index('name', 'idx_degrees_name');
        });

        // فهارس للجداول الأخرى
        Schema::table('reserved_thesis_titles', function (Blueprint $table) {
            $table->index('university', 'idx_reserved_university');
            $table->index('specialization', 'idx_reserved_specialization');
            $table->index('degree', 'idx_reserved_degree');
            $table->index('date', 'idx_reserved_date');
        });

        Schema::table('archive_theses', function (Blueprint $table) {
            $table->index(['university_id', 'specialization_id'], 'idx_archive_uni_spec');
            $table->index('created_at', 'idx_archive_created');
        });
    }

    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropIndex('idx_uni_spec_deg');
            $table->dropIndex('idx_year_degree');
            $table->dropIndex('idx_theses_created');
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->dropIndex('idx_authors_name');
        });

        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex('idx_universities_name');
        });

        Schema::table('specializations', function (Blueprint $table) {
            $table->dropIndex('idx_specializations_name');
        });

        Schema::table('degrees', function (Blueprint $table) {
            $table->dropIndex('idx_degrees_name');
        });

        Schema::table('reserved_thesis_titles', function (Blueprint $table) {
            $table->dropIndex('idx_reserved_university');
            $table->dropIndex('idx_reserved_specialization');
            $table->dropIndex('idx_reserved_degree');
            $table->dropIndex('idx_reserved_date');
        });

        Schema::table('archive_theses', function (Blueprint $table) {
            $table->dropIndex('idx_archive_uni_spec');
            $table->dropIndex('idx_archive_created');
        });
    }
}