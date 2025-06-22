<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Thesis;
use App\Models\Author;
use App\Models\University;
use App\Models\Degree;
use App\Models\Specialization;
use App\Models\ArchiveThesis;
use App\Models\ThesisTitlesSimple;
use App\Models\ReservedThesisTitle;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Helpers\PdfPathHelper;

class ThesisController extends Controller
{
    public function latestTheses()
    {
        $theses = Thesis::with(['author', 'university', 'specialization', 'degree'])
            ->latest('id')
            ->take(10)
            ->get();
        $result = $theses->map(function($thesis) {
            return [
                'id' => $thesis->id,
                'title' => $thesis->title,
                'year' => $thesis->year,
                'pdf_path' => $thesis->pdf_path ? '/api/pdf/' . PdfPathHelper::encryptPath($thesis->pdf_path) : null,
                'university' => $thesis->university ? [
                    'id' => $thesis->university->id,
                    'name' => $thesis->university->name,
                ] : null,
                'specialization' => $thesis->specialization ? [
                    'id' => $thesis->specialization->id,
                    'name' => $thesis->specialization->name,
                ] : null,
                'degree' => $thesis->degree ? [
                    'id' => $thesis->degree->id,
                    'name' => $thesis->degree->name,
                ] : null,
                'author' => $thesis->author ? [
                    'id' => $thesis->author->id,
                    'name' => $thesis->author->name,
                ] : null,
            ];
        });
        return response()->json($result->values());
    }

    public function searchTheses(Request $request)
    {
        $query = Thesis::with(['author', 'university', 'specialization', 'degree']);
        if ($request->filled('author')) {
            $query->whereHas('author', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->author . '%');
            });
        }
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('specialization_id')) {
            $query->where('specialization_id', $request->specialization_id);
        }
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->university_id);
        }
        if ($request->filled('degree_id')) {
            $query->where('degree_id', $request->degree_id);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        $theses = $query->latest('id')->get();
        $result = $theses->map(function($thesis) {
            return [
                'id' => $thesis->id,
                'title' => $thesis->title,
                'year' => $thesis->year,
                'pdf_path' => $thesis->pdf_path ? '/api/pdf/' . PdfPathHelper::encryptPath($thesis->pdf_path) : null,
                'university' => $thesis->university ? [
                    'id' => $thesis->university->id,
                    'name' => $thesis->university->name,
                ] : null,
                'specialization' => $thesis->specialization ? [
                    'id' => $thesis->specialization->id,
                    'name' => $thesis->specialization->name,
                ] : null,
                'degree' => $thesis->degree ? [
                    'id' => $thesis->degree->id,
                    'name' => $thesis->degree->name,
                ] : null,
                'author' => $thesis->author ? [
                    'id' => $thesis->author->id,
                    'name' => $thesis->author->name,
                ] : null,
            ];
        });
        return response()->json($result->values());
    }

    public function searchThesesForGuests(Request $request)
    {
        $query = Thesis::with(['author', 'university', 'specialization', 'degree']);
        if ($request->filled('author')) {
            $query->whereHas('author', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->author . '%');
            });
        }
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('specialization_id')) {
            $query->where('specialization_id', $request->specialization_id);
        }
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->university_id);
        }
        if ($request->filled('degree_id')) {
            $query->where('degree_id', $request->degree_id);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        $theses = $query->latest('id')->get();
        $result = $theses->map(function($thesis) {
            return [
                'title' => $thesis->title,
                'year' => $thesis->year,
                'pdf_path' => $thesis->pdf_path ? request()->getSchemeAndHttpHost() . '/api/pdf/' . PdfPathHelper::encryptPath($thesis->pdf_path) : null,
                'university' => $thesis->university ? $thesis->university->name : null,
                'specialization' => $thesis->specialization ? $thesis->specialization->name : null,
                'degree' => $thesis->degree ? $thesis->degree->name : null,
                'author' => $thesis->author ? $thesis->author->name : null,
            ];
        });
        return response()->json($result->values());
    }

    public function storeThesis(Request $request)
    {
        // تحقق من الحقول المطلوبة، واجعل اللغة اختيارية
        $validated = $request->validate([
            'title' => 'required|string|max:1024',
            'university_id' => 'required|exists:universities,id',
            'specialization_id' => 'required|exists:specializations,id',
            'degree_id' => 'required|exists:degrees,id',
            'author_name' => 'required|string|max:255',
            'pdf' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

        // السنة والشهر من النظام
        $year = now()->year;
        $month = now()->month;
        // اللغة: إذا لم تُرسل في الطلب تُضاف "عربي" افتراضيًا
        $language = $request->input('language');
        if (!$language || trim($language) === '') {
            $language = 'عربي';
        }

        // إنشاء أو جلب الباحث
        $author = Author::firstOrCreate(['name' => $validated['author_name']]);

        // جلب أسماء الدرجة والتخصص والجامعة
        $degree = Degree::find($validated['degree_id']);
        $specialization = Specialization::find($validated['specialization_id']);
        $university = University::find($validated['university_id']);

        // تجهيز أسماء المجلدات بشكل آمن (إزالة الشرطات المائلة فقط، مع إبقاء المسافات)
        $degreeFolder = preg_replace("#/[/\\\\]+#u", '', $degree ? $degree->name : 'بدون_درجة');
        $specializationFolder = preg_replace("#/[/\\\\]+#u", '', $specialization ? $specialization->name : 'بدون_تخصص');
        $authorFolder = preg_replace("#/[/\\\\]+#u", '', $author->name);
        $languageFolder = preg_replace("#/[/\\\\]+#u", '', $language);
        $targetDir = "pdfs/new_directory/{$year}/{$month}/{$languageFolder}/{$degreeFolder}/{$specializationFolder}/{$authorFolder}";

        // حفظ ملف PDF في المسار الجديد
        $pdfFile = $request->file('pdf');
        $pdfName = preg_replace("#[\s/\\\\]+#u", '_', $pdfFile->getClientOriginalName());
        $relativePath = "$targetDir/$pdfName";
        $pdfPath = $pdfFile->storeAs($targetDir, $pdfName, 'public');

        // حفظ المسار في قاعدة البيانات مع /storage/ في البداية
        $dbPdfPath = '/storage/' . $relativePath;

        // التأكد من ربط التخصص بالجامعة
        $specializationUniversityExists = \DB::table('specialization_university')
            ->where('university_id', $validated['university_id'])
            ->where('specialization_id', $validated['specialization_id'])
            ->exists();
        if (!$specializationUniversityExists) {
            \DB::table('specialization_university')->insert([
                'university_id' => $validated['university_id'],
                'specialization_id' => $validated['specialization_id'],
            ]);
        }

        // إنشاء الرسالة في قاعدة البيانات
        $thesis = Thesis::create([
            'title' => $validated['title'],
            'year' => $year,
            'pdf_path' => $dbPdfPath,
            'university_id' => $validated['university_id'],
            'specialization_id' => $validated['specialization_id'],
            'degree_id' => $validated['degree_id'],
            'author_id' => $author->id,
            'language' => $language,
        ]);

        // تجهيز بيانات ملف الجيسون
        $jsonData = [
            'id' => (string)$thesis->id,
            'اسم الشخص' => $author->name,
            'التخصص' => $specialization ? $specialization->name : '',
            'عنوان الرسالة' => $validated['title'],
            'اسم الجامعة او الكلية' => $university ? $university->name : '',
            'التاريخ' => $year,
            'الدرجة العلمية' => $degree ? $degree->name : '',
            'اللغة' => $language,
            'source' => 'all_csv.json',
        ];
        $jsonFileName = $authorFolder . '.json';
        \Storage::disk('public')->put("$targetDir/$jsonFileName", json_encode($jsonData, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

        // لوجات للتتبع
        \Log::info('[THESIS STORE] PDF Path: ' . $dbPdfPath);
        \Log::info('[THESIS STORE] JSON Path: ' . "$targetDir/$jsonFileName");
        \Log::info('[THESIS STORE] Thesis DB: ' . json_encode($thesis));

        return response()->json([
            'message' => 'تمت إضافة الرسالة بنجاح',
            'thesis' => $thesis,
            'author_name' => $author->name,
            'pdf_path' => $dbPdfPath,
            'json_path' => "$targetDir/$jsonFileName"
        ], 201);
    }

    public function updateThesis(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author_id' => 'sometimes|required|exists:authors,id',
            'university_id' => 'sometimes|required|exists:universities,id',
            'degree_id' => 'sometimes|required|exists:degrees,id',
            'specialization_id' => 'sometimes|required|exists:specializations,id',
            'year' => 'sometimes|required|integer|min:1900|max:' . date('Y'),
            'pdf_path' => 'sometimes|required|file|mimes:pdf|max:2048',
        ]);

        $thesis = Thesis::findOrFail($id);

        if ($request->hasFile('pdf_path')) {
            // Delete old file
            Storage::disk('public')->delete($thesis->pdf_path);

            $path = $request->file('pdf_path')->store('theses', 'public');
            $thesis->pdf_path = $path;
        }

        $thesis->update($request->only('title', 'author_id', 'university_id', 'degree_id', 'specialization_id', 'year'));

        return response()->json([
            'id' => $thesis->id,
            'title' => $thesis->title,
            'year' => $thesis->year,
            'pdf_path' => '/api/pdf/' . PdfPathHelper::encryptPath($thesis->pdf_path),
            'author' => $thesis->author->only('id', 'name'),
            'university' => $thesis->university->only('id', 'name'),
            'specialization' => $thesis->specialization->only('id', 'name'),
            'degree' => $thesis->degree->only('id', 'name'),
        ]);
    }

    public function deleteThesis($id)
    {
        $thesis = Thesis::findOrFail($id);

        // Delete file from storage
        Storage::disk('public')->delete($thesis->pdf_path);

        $thesis->delete();

        return response()->json(null, 204);
    }

    public function allYears()
    {
        $years = Thesis::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        return response()->json($years);
    }

    public function servePdf($token)
    {
        $path = PdfPathHelper::decryptPath($token);

        return response()->file(storage_path('app/public/' . $path));
    }

    public function archiveThesis(Request $request)
    {
        $request->validate([
            'thesis_id' => 'required|exists:theses,id',
        ]);

        $thesis = Thesis::findOrFail($request->thesis_id);

        // نقل الملف إلى الأرشيف
        $archivePath = 'archived_theses/' . date('Y/m/d');
        $fileName = basename($thesis->pdf_path);
        $newFilePath = $archivePath . '/' . $fileName;

        // تأكد من عدم وجود ملف بنفس الاسم في الأرشيف
        $counter = 1;
        while (Storage::disk('public')->exists($newFilePath)) {
            $newFilePath = $archivePath . '/' . pathinfo($fileName, PATHINFO_FILENAME) . "_{$counter}." . pathinfo($fileName, PATHINFO_EXTENSION);
            $counter++;
        }

        // نقل الملف
        Storage::disk('public')->move($thesis->pdf_path, $newFilePath);

        // تحديث مسار الملف في قاعدة البيانات
        $thesis->pdf_path = '/storage/' . $newFilePath;
        $thesis->save();

        return response()->json([
            'message' => 'تم أرشفة الرسالة بنجاح',
            'thesis' => $thesis,
        ]);
    }

    public function restoreThesis(Request $request)
    {
        $request->validate([
            'thesis_id' => 'required|exists:archived_theses,id',
        ]);

        $archivedThesis = ArchiveThesis::findOrFail($request->thesis_id);

        // استعادة الملف من الأرشيف
        $originalPath = str_replace('/storage/', '', $archivedThesis->pdf_path);
        $newFilePath = 'theses/' . basename($originalPath);

        // تأكد من عدم وجود ملف بنفس الاسم في المسار الجديد
        $counter = 1;
        while (Storage::disk('public')->exists($newFilePath)) {
            $newFilePath = 'theses/' . pathinfo($originalPath, PATHINFO_FILENAME) . "_{$counter}." . pathinfo($originalPath, PATHINFO_EXTENSION);
            $counter++;
        }

        // نقل الملف
        Storage::disk('public')->move($archivedThesis->pdf_path, $newFilePath);

        // حذف السجل من الأرشيف
        $archivedThesis->delete();

        return response()->json([
            'message' => 'تم استعادة الرسالة بنجاح',
            'thesis' => [
                'id' => $archivedThesis->thesis_id,
                'title' => $archivedThesis->title,
                'pdf_path' => '/api/pdf/' . PdfPathHelper::encryptPath($newFilePath),
            ],
        ]);
    }

    public function simpleThesisTitles()
    {
        $titles = ThesisTitlesSimple::all();
        return response()->json($titles);
    }

    public function reservedThesisTitles()
    {
        $titles = ReservedThesisTitle::all();
        return response()->json($titles);
    }
}
