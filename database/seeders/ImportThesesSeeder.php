<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\University;
use App\Models\Specialization;
use App\Models\Degree;
use App\Models\Author;
use App\Models\Thesis;

class ImportThesesSeeder extends Seeder
{
    public function run(): void
    {
        // حذف جميع بيانات جدول theses قبل التعبئة
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('theses')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $basePath = base_path('storage/app/public/pdfs/json_content');
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($basePath));
        $logFile = base_path('storage/app/public/pdfs/import_log.txt');
        $imported = 0;
        $skipped = 0;
        file_put_contents($logFile, "\n=== Import Started at ".date('Y-m-d H:i:s')." ===\n", FILE_APPEND);
        foreach ($rii as $file) {
            if ($file->isDir()) continue;
            if (strtolower($file->getExtension()) !== 'json') continue;
            $jsonPath = $file->getPathname();
            $dir = $file->getPath();
            $pdfPath = null;
            foreach (scandir($dir) as $f) {
                if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf') {
                    $pdfPath = $dir . DIRECTORY_SEPARATOR . $f;
                    break;
                }
            }
            if (!$pdfPath) {
                file_put_contents($logFile, "[SKIP] No PDF for: $jsonPath\n", FILE_APPEND);
                $skipped++;
                continue;
            }
            $json = file_get_contents($jsonPath);
            $data = json_decode($json, true);
            if (!$data) {
                file_put_contents($logFile, "[SKIP] Invalid JSON: $jsonPath\n", FILE_APPEND);
                $skipped++;
                continue;
            }
            // Get university and specialization by name only (do not create new)
            $university = University::where('name', $data['اسم الجامعة او الكلية'])->first();
            $specialization = Specialization::where('name', $data['التخصص'])->first();
            if (!$university || !$specialization) {
                file_put_contents($logFile, "[SKIP] Not found: $jsonPath | Univ: {$data['اسم الجامعة او الكلية']} | Spec: {$data['التخصص']}\n", FILE_APPEND);
                $skipped++;
                continue;
            }
            $degree = Degree::firstOrCreate(['name' => $data['الدرجة العلمية']]);
            $author = Author::firstOrCreate(['name' => $data['اسم الشخص']]);
            $university->specializations()->syncWithoutDetaching([$specialization->id]);
            $thesis = Thesis::firstOrCreate([
                'title' => $data['عنوان الرسالة'],
                'year' => $data['التاريخ'],
                'university_id' => $university->id,
                'specialization_id' => $specialization->id,
                'degree_id' => $degree->id,
                'author_id' => $author->id,
            ], [
                'pdf_path' => $pdfPath,
            ]);
            if ($thesis->wasRecentlyCreated) {
                file_put_contents($logFile, "[OK] Imported: $jsonPath | PDF: $pdfPath\n", FILE_APPEND);
                $imported++;
            } else {
                file_put_contents($logFile, "[SKIP] Duplicate: $jsonPath\n", FILE_APPEND);
                $skipped++;
            }
        }
        file_put_contents($logFile, "=== Import Finished. Imported: $imported, Skipped: $skipped ===\n", FILE_APPEND);
    }
}
