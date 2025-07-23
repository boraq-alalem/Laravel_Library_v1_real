<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddOptimizedIndexes extends Migration
{
    public function up()
    {
        // تم نقل جميع الفهارس إلى مايجريشن 2025_01_01 لتجنب التعارض
        // هذه المايجريشن فارغة الآن
    }

    public function down()
    {
        // لا يوجد شيء لحذفه
    }
}