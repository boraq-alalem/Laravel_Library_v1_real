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
        $basePath = 'C:/Users/hammam/Desktop/omar_final/test_6/json_content';
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($basePath));
        $logFile = 'C:/Users/hammam/Desktop/design/Laravel/import_log.txt';
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
            $university = University::firstOrCreate(['name' => $data['اسم الجامعة او الكلية']]);
            $specialization = Specialization::firstOrCreate(['name' => $data['التخصص']]);
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
