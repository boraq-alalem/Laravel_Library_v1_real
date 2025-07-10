<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptimizedIndexes extends Migration
{
    public function up()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->index(['degree_id', 'year'], 'idx_degree_year');
            $table->index(['university_id', 'specialization_id'], 'idx_uni_spec');
            $table->index(['author_id', 'year'], 'idx_author_year');
            $table->index('title', 'idx_title');
            $table->index('created_at', 'idx_created');
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->index('name', 'idx_author_name');
        });

        Schema::table('universities', function (Blueprint $table) {
            $table->index('name', 'idx_university_name');
        });

        Schema::table('specializations', function (Blueprint $table) {
            $table->index('name', 'idx_specialization_name');
        });
    }

    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropIndex('idx_degree_year');
            $table->dropIndex('idx_uni_spec');
            $table->dropIndex('idx_author_year');
            $table->dropIndex('idx_title');
            $table->dropIndex('idx_created');
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->dropIndex('idx_author_name');
        });

        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex('idx_university_name');
        });

        Schema::table('specializations', function (Blueprint $table) {
            $table->dropIndex('idx_specialization_name');
        });
    }
}