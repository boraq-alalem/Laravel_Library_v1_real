<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->index(['author_id', 'university_id']);
            $table->index(['specialization_id', 'degree_id']);
            $table->index('year');
            $table->index('created_at');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('created_at');
        });
    }
    
    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropIndex(['author_id', 'university_id']);
            $table->dropIndex(['specialization_id', 'degree_id']);
            $table->dropIndex(['year']);
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['created_at']);
        });
    }
};