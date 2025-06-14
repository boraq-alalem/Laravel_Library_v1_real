<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportReservedThesisTitlesSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = storage_path('app/all_data.json');
        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data) return;

        foreach ($data as $row) {
            // تعبئة الجدول thesis_titles_simple فقط
            \DB::table('thesis_titles_simple')->updateOrInsert([
                'title' => $row['عنوان الرسالة'] ?? null,
            ], [
                'person_name' => $row['اسم الشخص'] ?? null,
                'university' => $row['اسم الجامعة او الكلية'] ?? null,
            ]);
            // تعبئة الجدول reserved_thesis_titles فقط
            \DB::table('reserved_thesis_titles')->updateOrInsert([
                'title' => $row['عنوان الرسالة'] ?? null,
            ], [
                'person_name' => $row['اسم الشخص'] ?? null,
                'language' => $row['اللغة'] ?? null,
                'degree' => $row['الدرجة العلمية'] ?? null,
                'specialization' => $row['التخصص'] ?? null,
                'university' => $row['اسم الجامعة او الكلية'] ?? null,
                'date' => $row['التاريخ'] ?? null,
            ]);
        }
    }
}
