<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReservedThesisTitle;

class ImportReservedThesisTitlesSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = storage_path('app/all_data.json');
        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data) return;
        foreach ($data as $row) {
            if (!isset($row['عنوان الرسالة'], $row['اسم الشخص'], $row['اسم الجامعة او الكلية'], $row['التخصص'], $row['الدرجة العلمية'], $row['التاريخ'])) continue;
            // تخطى إذا كان موجود مسبقاً
            if (ReservedThesisTitle::where('title', $row['عنوان الرسالة'])
                ->where('person_name', $row['اسم الشخص'])
                ->where('university', $row['اسم الجامعة او الكلية'])
                ->where('specialization', $row['التخصص'])
                ->where('degree', $row['الدرجة العلمية'])
                ->where('date', $row['التاريخ'])
                ->exists()) continue;
            ReservedThesisTitle::create([
                'title' => $row['عنوان الرسالة'],
                'person_name' => $row['اسم الشخص'],
                'university' => $row['اسم الجامعة او الكلية'],
                'specialization' => $row['التخصص'],
                'degree' => $row['الدرجة العلمية'],
                'date' => $row['التاريخ'],
            ]);
        }
    }
}
