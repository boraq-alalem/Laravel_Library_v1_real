<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ThesisTitlesSimple;

class ImportThesisTitlesSimpleSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = storage_path('app/all_data.json');
        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data) return;
        foreach ($data as $row) {
            if (!isset($row['عنوان الرسالة'], $row['اسم الشخص'], $row['اسم الجامعة او الكلية'])) continue;
            // تخطى العنوان إذا كان موجود مسبقاً
            if (ThesisTitlesSimple::where('title', $row['عنوان الرسالة'])->exists()) continue;
            ThesisTitlesSimple::create([
                'title' => $row['عنوان الرسالة'],
                'person_name' => $row['اسم الشخص'],
                'university' => $row['اسم الجامعة او الكلية'],
            ]);
        }
    }
}
