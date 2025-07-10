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
use Illuminate\Support\Facades\Cache;
use App\Helpers\PdfPathHelper;




class StatsController extends Controller
{
    // إحصائيات عامة
    public function index()
    {
        return Cache::remember('stats_index', 600, function() {
            $totalTheses = Thesis::count();
            $masterDegree = Degree::where('name', 'like', '%ماجستير%')->first();
            $phdDegree = Degree::where('name', 'like', '%دكتوراه%')->first();
            $masterTheses = $masterDegree ? Thesis::where('degree_id', $masterDegree->id)->count() : 0;
            $phdTheses = $phdDegree ? Thesis::where('degree_id', $phdDegree->id)->count() : 0;
            $totalAuthors = Author::count();
            $totalUniversities = University::count();
            $totalSpecializations = Specialization::count();

            return response()->json([
                'total_theses' => $totalTheses,
                'master_theses' => $masterTheses,
                'phd_theses' => $phdTheses,
                'total_authors' => $totalAuthors,
                'total_universities' => $totalUniversities,
                'total_specializations' => $totalSpecializations,
            ]);
        });
    }
    public function storeUniversity(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|unique:universities,id',
            'name' => 'required|string|unique:universities,name',
        ]);
        $university = new University();
        $university->id = $validated['id'];
        $university->name = $validated['name'];
        $university->save();
        return response()->json(['id' => $university->id, 'name' => $university->name], 201);
    }

    // حذف جامعة
    public function deleteUniversity($id)
    {
        $university = University::findOrFail($id);
        $university->delete();
        return response()->json(['message' => 'تم حذف الجامعة بنجاح']);
    }

    // إضافة تخصص جديد
    public function storeSpecialization(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|unique:specializations,id',
            'name' => 'required|string|unique:specializations,name',
        ]);
        $specialization = new Specialization();
        $specialization->id = $validated['id'];
        $specialization->name = $validated['name'];
        $specialization->save();
        return response()->json(['id' => $specialization->id, 'name' => $specialization->name], 201);
    }

    // حذف تخصص
    public function deleteSpecialization($id)
    {
        $specialization = Specialization::findOrFail($id);
        $specialization->delete();
        return response()->json(['message' => 'تم حذف التخصص بنجاح']);
    }

    // بحث عن تخصص
    public function searchSpecializations(Request $request)
    {
        $query = Specialization::query();
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        $specializations = $query->get(['id', 'name']);
        return response()->json($specializations);
    }

    public function latestTheses(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 14);
        
        return Cache::remember('latest_theses_' . $page . '_' . $perPage, 7200, function() use ($page, $perPage) {
            $total = Thesis::count();
            $theses = Thesis::select('id', 'title', 'year', 'pdf_path', 'author_id', 'university_id', 'specialization_id', 'degree_id')
                ->with([
                    'author:id,name',
                    'university:id,name',
                    'specialization:id,name',
                    'degree:id,name'
                ])
                ->latest('id')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            $result = $theses->map(function($thesis) {
                return [
                    'id' => $thesis->id,
                    'title' => $thesis->title,
                    'year' => $thesis->year,
                    // تشفير مسار PDF فقط بدون أي دومين أو بادئة
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
            return response()->json([
                'data' => $result->values(),
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                    'from' => ($page - 1) * $perPage + 1,
                    'to' => min($page * $perPage, $total)
                ]
            ]);
        });
    }

    public function searchTheses(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 14);
        $searchParams = [
            'title' => $request->get('title'),
            'author' => $request->get('author'),
            'degree_id' => $request->get('degree_id'),
            'university_id' => $request->get('university_id'),
            'specialization_id' => $request->get('specialization_id'),
            'year' => $request->get('year'),
            'page' => $page,
            'per_page' => $perPage
        ];
        $cacheKey = 'search_' . md5(json_encode(array_filter($searchParams)));
        
        return Cache::remember($cacheKey, 1800, function() use ($request, $page, $perPage) {
            $query = Thesis::select('id', 'title', 'year', 'author_id', 'university_id', 'specialization_id', 'degree_id')
                ->with([
                    'author:id,name',
                    'university:id,name',
                    'specialization:id,name', 
                    'degree:id,name'
                ]);
            if ($request->filled('author')) {
                $query->whereHas('author', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->author . '%');
                });
            }
            if ($request->filled('title')) {
                $query->whereRaw("MATCH(title) AGAINST (? IN BOOLEAN MODE)", [$request->title]);
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
            
            $total = $query->count();
            $theses = $query->latest('id')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            $result = $theses->map(function($thesis) {
                return [
                    'id' => $thesis->id,
                    'title' => $thesis->title,
                    'year' => $thesis->year,
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
            return response()->json([
                'data' => $result->values(),
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                    'from' => ($page - 1) * $perPage + 1,
                    'to' => min($page * $perPage, $total)
                ]
            ]);
        });
    }

    public function allSpecializations()
    {
        return response()->json(Specialization::all(['id', 'name']));
    }

    public function allUniversities()
    {
        return response()->json(University::all(['id', 'name']));
    }

    public function allDegrees()
    {
        return response()->json(Degree::all(['id', 'name']));
    }

    public function allYears()
    {
        $years = Thesis::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        return response()->json($years);
    }

    public function updateThesis(Request $request, $id)
    {
        $thesis = Thesis::findOrFail($id);
        $oldDegree = $thesis->degree ? $thesis->degree->name : 'بدون_درجة';
        $oldSpecialization = $thesis->specialization ? $thesis->specialization->name : 'بدون_تخصص';
        $oldAuthor = $thesis->author ? $thesis->author->name : 'بدون_اسم';
        $oldDegreeFolder = preg_replace('/\s+/u', '_', $oldDegree);
        $oldSpecializationFolder = preg_replace('/\s+/u', '_', $oldSpecialization);
        $oldAuthorFolder = preg_replace('/\s+/u', '_', $oldAuthor);
        $basePath = 'pdfs/json_content_new';
        $oldDir = "$basePath/$oldDegreeFolder/$oldSpecializationFolder/$oldAuthorFolder";
        $oldPdfPath = $thesis->pdf_path;
        $oldJsonFile = $oldAuthorFolder . '.json';

        // تحديث اسم الشخص إذا تم تعديله
        if ($request->filled('author_name')) {
            $author = Author::firstOrCreate(['name' => $request->author_name]);
            $request->merge(['author_id' => $author->id]);
        }

        // تحديث الحقول بدون pdf_path إذا لم يتم رفع ملف جديد
        $updateData = $request->only([
            'title', 'university_id', 'specialization_id', 'degree_id', 'author_id', 'year'
        ]);
        // إذا لم يرسل المستخدم year، احفظ السنة والشهر الحاليين
        if (!$request->filled('year')) {
            $currentYear = date('Y');
            $currentMonth = date('m');
            $updateData['year'] = $currentYear . '-' . $currentMonth;
        }
        // إذا تم رفع ملف PDF جديد
        if ($request->hasFile('pdf')) {
            // حذف ملف PDF القديم إذا كان موجوداً
            if ($oldPdfPath) {
                $oldPath = str_replace('/storage/', '', $oldPdfPath);
                \Storage::disk('public')->delete($oldPath);
            }
            $pdfFile = $request->file('pdf');
            $pdfName = $pdfFile->getClientOriginalName();
            // جلب القيم الجديدة بعد التحديث
            $newDegree = $request->degree_id ? (\App\Models\Degree::find($request->degree_id)->name ?? 'بدون_درجة') : $oldDegree;
            $newSpecialization = $request->specialization_id ? (\App\Models\Specialization::find($request->specialization_id)->name ?? 'بدون_تخصص') : $oldSpecialization;
            $newAuthor = $request->author_id ? (\App\Models\Author::find($request->author_id)->name ?? 'بدون_اسم') : $oldAuthor;
            $newDegreeFolder = preg_replace('/\s+/u', '_', $newDegree);
            $newSpecializationFolder = preg_replace('/\s+/u', '_', $newSpecialization);
            $newAuthorFolder = preg_replace('/\s+/u', '_', $newAuthor);
            $newDir = "$basePath/$newDegreeFolder/$newSpecializationFolder/$newAuthorFolder";
            $relativePath = "$newDir/$pdfName";
            $pdfPath = $pdfFile->storeAs($newDir, $pdfName, 'public');
            $updateData['pdf_path'] = '/storage/' . $relativePath;
        }
        $thesis->update($updateData);
        $thesis->refresh();

        // جلب القيم الجديدة بعد التحديث
        $newDegree = $thesis->degree ? $thesis->degree->name : 'بدون_درجة';
        $newSpecialization = $thesis->specialization ? $thesis->specialization->name : 'بدون_تخصص';
        $newAuthor = $thesis->author ? $thesis->author->name : 'بدون_اسم';
        $newDegreeFolder = preg_replace('/\s+/u', '_', $newDegree);
        $newSpecializationFolder = preg_replace('/\s+/u', '_', $newSpecialization);
        $newAuthorFolder = preg_replace('/\s+/u', '_', $newAuthor);
        $newDir = "$basePath/$newDegreeFolder/$newSpecializationFolder/$newAuthorFolder";

        // إذا تغير المسار
        if ($oldDir !== $newDir) {
            // نقل ملف PDF القديم إذا لم يتم رفع ملف جديد
            if (!$request->hasFile('pdf') && $oldPdfPath && \Storage::disk('public')->exists(str_replace('/storage/', '', $oldPdfPath))) {
                $pdfName = basename($oldPdfPath);
                $newPdfPath = "$newDir/$pdfName";
                \Storage::disk('public')->makeDirectory($newDir);
                \Storage::disk('public')->move(str_replace('/storage/', '', $oldPdfPath), $newPdfPath);
                $thesis->update(['pdf_path' => '/storage/' . $newPdfPath]);
            }
            // تحديث أو إنشاء ملف JSON جديد بالقيم الجديدة
            $newJsonFile = $newAuthorFolder . '.json';
            $jsonData = [
                'id' => (string)$thesis->id,
                'اسم الشخص' => $newAuthor,
                'التخصص' => $newSpecialization,
                'عنوان الرسالة' => $thesis->title,
                'اسم الجامعة او الكلية' => $thesis->university ? $thesis->university->name : '',
                'التاريخ' => $thesis->year,
                'الدرجة العلمية' => $newDegree,
                'source' => 'all_csv.json',
            ];
            \Storage::disk('public')->put("$newDir/$newJsonFile", json_encode($jsonData, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
            // حذف ملف JSON القديم إذا كان موجوداً
            if (\Storage::disk('public')->exists("$oldDir/$oldJsonFile")) {
                \Storage::disk('public')->delete("$oldDir/$oldJsonFile");
            }
            // حذف المجلد القديم دائماً
            \Storage::disk('public')->deleteDirectory($oldDir);
        }
        return response()->json(['message' => 'تم التعديل بنجاح', 'thesis' => $thesis->fresh()]);
    }

    public function deleteThesis($id)
    {
        $thesis = Thesis::findOrFail($id);
        // نقل جميع البيانات بما فيها id
        $data = $thesis->toArray();
        ArchiveThesis::create($data);
        $thesis->delete();
        return response()->json(['message' => 'تم نقل الرسالة إلى الأرشيف وحذفها من جدول الرسائل']);
    }

    public function getArchivedTheses()
    {
        $theses = ArchiveThesis::with(['author', 'university', 'specialization', 'degree'])->latest('id')->get();
        $result = $theses->map(function($thesis) {
            return [
                'id' => $thesis->id,
                'title' => $thesis->title,
                'year' => $thesis->year,
                // تشفير مسار PDF للأرشيف
                'pdf_path' => $thesis->pdf_path ? request()->getSchemeAndHttpHost() . '/api/pdf/' . PdfPathHelper::encryptPath($thesis->pdf_path) : null,
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

    public function deleteArchivedThesis($id)
    {
        $thesis = ArchiveThesis::findOrFail($id);
        // حذف مجلد الشخص من التخزين إذا كان موجوداً
        if ($thesis->pdf_path) {
            $pdfPath = $thesis->pdf_path;
            $dir = dirname($pdfPath);
            $relativeDir = ltrim(str_replace('/storage/', '', $dir), '/');
            Storage::disk('public')->deleteDirectory($relativeDir);
        }
        $thesis->delete();
        return response()->json(['message' => 'تم حذف الرسالة والمجلد نهائياً من الأرشيف']);
    }

    public function universitiesWithSpecializations()
    {
        // استعلام مباشر من جدول الوسيط فقط
        $data = \DB::table('specialization_university')
            ->join('universities', 'specialization_university.university_id', '=', 'universities.id')
            ->join('specializations', 'specialization_university.specialization_id', '=', 'specializations.id')
            ->select('universities.id as university_id', 'universities.name as university_name', 'specializations.id as specialization_id', 'specializations.name as specialization_name')
            ->get();

        // إعادة ترتيب البيانات بحيث كل جامعة تحتها تخصصاتها
        $result = [];
        foreach ($data as $row) {
            if (!isset($result[$row->university_id])) {
                $result[$row->university_id] = [
                    'id' => $row->university_id,
                    'name' => $row->university_name,
                    'specializations' => [],
                ];
            }
            $result[$row->university_id]['specializations'][] = [
                'id' => $row->specialization_id,
                'name' => $row->specialization_name,
            ];
        }
        return response()->json(array_values($result));
    }

    public function addSpecializationToUniversity(Request $request, $universityId)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'specialization_id' => 'required|exists:specializations,id',
        ]);
        // إضافة التخصص للجامعة بدون تكرار
        \DB::table('specialization_university')->updateOrInsert([
            'university_id' => $validated['university_id'],
            'specialization_id' => $validated['specialization_id'],
        ], []);
        return response()->json(['message' => 'تمت إضافة التخصص للجامعة بنجاح']);
    }

    public function searchUniversities(Request $request)
    {
        $query = University::query();
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        $universities = $query->get(['id', 'name']);
        return response()->json($universities);
    }

    public function storeThesis(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:1024',
            'year' => 'required|string',
            'university_id' => 'required|exists:universities,id',
            'specialization_id' => 'required|exists:specializations,id',
            'degree_id' => 'required|exists:degrees,id',
            'author_name' => 'required|string|max:255',
            'pdf' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

        // التحقق من وجود عنوان الرسالة مسبقاً
        if (Thesis::where('title', $validated['title'])->exists()) {
            return response()->json([
                'message' => 'العنوان موجود بالفعل'
            ], 409);
        }

        // إنشاء أو جلب الباحث
        $author = Author::firstOrCreate(['name' => $validated['author_name']]);

        // جلب أسماء الدرجة والتخصص والجامعة
        $degree = Degree::find($validated['degree_id']);
        $specialization = Specialization::find($validated['specialization_id']);
        $university = University::find($validated['university_id']);

        // تجهيز المسار المطلوب
        // $basePath = 'pdfs/json_content_new'; // المسار القديم (تم تعليقه بناءً على طلبك)
        $basePath = 'pdfs/json_content_new'; // المسار الجديد لحفظ الرسائل الجديدة
        $degreeName = $degree ? $degree->name : 'بدون_درجة';
        $specializationName = $specialization ? $specialization->name : 'بدون_تخصص';
        $authorName = $author->name;
        // transliterate/replace spaces for folder names
        $degreeFolder = preg_replace('/\s+/u', '_', $degreeName);
        $specializationFolder = preg_replace('/\s+/u', '_', $specializationName);
        $authorFolder = preg_replace('/\s+/u', '_', $authorName);
        $targetDir = "$basePath/$degreeFolder/$specializationFolder/$authorFolder";

        // حفظ ملف PDF في المسار الجديد باسم الشخص
        $pdfFile = $request->file('pdf');
        $pdfExtension = $pdfFile->getClientOriginalExtension();
        $pdfName = $authorFolder . '.' . $pdfExtension; // اسم الملف = اسم الشخص
        $relativePath = "$targetDir/$pdfName";
        $pdfPath = $pdfFile->storeAs($targetDir, $pdfName, 'public');
        
        // تحديد المسار النسبي الذي سيخزن في قاعدة البيانات
        $dbPdfPath = '/storage/' . $relativePath;

        // حفظ السنة والشهر معاً في حقل year
        $currentYear = date('Y');
        $currentMonth = date('m');
        $yearMonth = $currentYear . '-' . $currentMonth;

        // إنشاء الرسالة في قاعدة البيانات
        $thesis = Thesis::create([
            'title' => $validated['title'],
            'year' => $yearMonth,
            'pdf_path' => $dbPdfPath,
            'university_id' => $validated['university_id'],
            'specialization_id' => $validated['specialization_id'],
            'degree_id' => $validated['degree_id'],
            'author_id' => $author->id,
        ]);

        // تجهيز بيانات ملف الجيسون
        $jsonData = [
            'id' => (string)$thesis->id,
            'اسم الشخص' => $author->name,
            'التخصص' => $specializationName,
            'عنوان الرسالة' => $validated['title'],
            'اسم الجامعة او الكلية' => $university ? $university->name : '',
            'التاريخ' => $validated['year'],
            'الدرجة العلمية' => $degreeName,
            'source' => 'all_csv.json',
        ];
        $jsonFileName = $authorFolder . '.json';
        \Storage::disk('public')->put("$targetDir/$jsonFileName", json_encode($jsonData, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

        return response()->json([
            'message' => 'تمت إضافة الرسالة بنجاح',
            'thesis' => $thesis,
            'author_name' => $author->name
        ], 201);
    }

    public function universitiesWithSpecializationsForGuests()
    {
        $data = \DB::table('specialization_university')
            ->join('universities', 'specialization_university.university_id', '=', 'universities.id')
            ->join('specializations', 'specialization_university.specialization_id', '=', 'specializations.id')
            ->select('universities.id as university_id', 'universities.name as university_name', 'specializations.name as specialization_name')
            ->get();

        $result = [];
        foreach ($data as $row) {
            if (!isset($result[$row->university_id])) {
                $result[$row->university_id] = [
                    'id' => $row->university_id,
                    'name' => $row->university_name,
                    'specializations' => [],
                ];
            }
            $result[$row->university_id]['specializations'][] = $row->specialization_name;
        }
        return response()->json(array_values($result));
    }

    public function restoreThesis($id)
    {
        $archived = ArchiveThesis::findOrFail($id);
        // نقل جميع البيانات بما فيها id
        $data = $archived->toArray();
        Thesis::create($data);
        $archived->delete();
        return response()->json(['message' => 'تمت استعادة الرسالة إلى جدول الرسائل بنجاح']);
    }

    // =====================
    // GRUDS APIs for thesis_titles_simple
    // =====================
    public function getThesisTitlesSimple()
    {
        $items = ThesisTitlesSimple::select('id', 'title', 'person_name', 'university')->get();
        return response()->json($items);
    }

    public function showThesisTitleSimple($id)
    {
        $item = ThesisTitlesSimple::select('id', 'title', 'person_name', 'university')->findOrFail($id);
        return response()->json($item);
    }

    public function storeThesisTitleSimple(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|unique:thesis_titles_simple,title',
            'person_name' => 'required|string',
            'university' => 'required|string',
        ]);
        $item = ThesisTitlesSimple::create($validated);
        return response()->json($item->only(['id', 'title', 'person_name', 'university']), 201);
    }

    public function updateThesisTitleSimple(Request $request, $id)
    {
        $item = ThesisTitlesSimple::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|unique:thesis_titles_simple,title,' . $id,
            'person_name' => 'required|string',
            'university' => 'required|string',
        ]);
        $item->update($validated);
        return response()->json($item->only(['id', 'title', 'person_name', 'university']));
    }

    public function deleteThesisTitleSimple($id)
    {
        $item = ThesisTitlesSimple::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    // جلب آخر 10 عناوين
    public function latestThesisTitlesSimple()
    {
        $items = ThesisTitlesSimple::select('id', 'title', 'person_name', 'university')->latest('id')->take(10)->get();
        return response()->json($items);
    }

    // البحث عن طريق العنوان (بحث غير حرفي)
    public function searchThesisTitlesSimple(Request $request)
    {
        $q = $request->input('q');
        $items = ThesisTitlesSimple::select('id', 'title', 'person_name', 'university')
            ->when($q, function($query) use ($q) {
                $query->where('title', 'like', "%$q%");
            })
            ->get();
        return response()->json($items);
    }

    public function storeThesisTitleSimpleFromQuery(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|unique:thesis_titles_simple,title',
            'person_name' => 'required|string',
            'university' => 'required|string',
        ]);
        $item = ThesisTitlesSimple::create($validated);
        return response()->json($item->only(['id', 'title', 'person_name', 'university']), 201);
    }

    // =====================
    // GRUDS APIs for reserved_thesis_titles
    // =====================
    public function getReservedThesisTitles()
    {
        $items = ReservedThesisTitle::select('id', 'title', 'person_name', 'university', 'specialization', 'degree', 'date')->get();
        return response()->json($items);
    }

    public function showReservedThesisTitle($id)
    {
        $item = ReservedThesisTitle::select('id', 'title', 'person_name', 'university', 'specialization', 'degree', 'date')->findOrFail($id);
        return response()->json($item);
    }

    // جلب آخر 10 عناصر
    public function latestReservedThesisTitles()
    {
        $items = ReservedThesisTitle::select('id', 'title', 'person_name', 'university', 'specialization', 'degree', 'date')
            ->latest('id')->take(10)->get();
        return response()->json($items);
    }

    // إضافة عنصر جديد
    public function storeReservedThesisTitle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'person_name' => 'required|string',
            'university' => 'required|string',
            'specialization' => 'required|string',
            'degree' => 'required|string',
            'date' => 'required|string',
        ]);
        // التحقق من وجود عنوان محجوز مسبقاً
        if (ReservedThesisTitle::where('title', $validated['title'])->exists()) {
            return response()->json([
                'message' => 'العنوان موجود بالفعل'
            ], 409);
        }
        $item = ReservedThesisTitle::create($validated);
        return response()->json($item, 201);
    }

    // تعديل عنصر
    public function updateReservedThesisTitle(Request $request, $id)
    {
        $item = ReservedThesisTitle::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string',
            'person_name' => 'required|string',
            'university' => 'required|string',
            'specialization' => 'required|string',
            'degree' => 'required|string',
            'date' => 'required|string',
        ]);
        $item->update($validated);
        return response()->json($item);
    }

    // حذف عنصر
    public function deleteReservedThesisTitle($id)
    {
        $item = ReservedThesisTitle::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    // البحث عن طريق العنوان فقط
    public function searchReservedThesisTitles(Request $request)
    {
        $q = $request->input('q');
        $items = ReservedThesisTitle::select('id', 'title', 'person_name', 'university', 'specialization', 'degree', 'date')
            ->when($q, function($query) use ($q) {
                $query->where('title', 'like', "%$q%");
            })
            ->get();
        return response()->json($items);
    }

    // البحث عن طريق اسم الشخص فقط في العناوين المحجوزة
    public function searchReservedThesisTitlesByPerson(Request $request)
    {
        $q = $request->input('q');
        $items = ReservedThesisTitle::select('id', 'title', 'person_name', 'university', 'specialization', 'degree', 'date')
            ->when($q, function($query) use ($q) {
                $query->where('person_name', 'like', "%$q%");
            })
            ->get();
        return response()->json($items);
    }

    // البحث عن طريق اسم الشخص فقط في العناوين المحجوزة (للضيوف بدون id)
    public function searchReservedThesisTitlesByPersonForGuests(Request $request)
    {
        $q = $request->input('q');
        $items = ReservedThesisTitle::select('title', 'person_name', 'university', 'specialization', 'degree', 'date')
            ->when($q, function($query) use ($q) {
                $query->where('person_name', 'like', "%$q%");
            })
            ->get();
        return response()->json($items);
    }

    // البحث عن طريق العنوان فقط للزوار (title, person_name, university)
    public function searchReservedThesisTitlesForGuests(Request $request)
    {
        $q = $request->input('q');
        $items = ReservedThesisTitle::select('title', 'person_name', 'university')
            ->when($q, function($query) use ($q) {
                $query->where('title', 'like', "%$q%");
            })
            ->get();
        return response()->json($items);
    }

    // جلب آخر 10 عناصر للزوار فقط (title, person_name, university)
    public function latestReservedThesisTitlesForGuests()
    {
        $items = ReservedThesisTitle::select('title', 'person_name', 'university')
            ->latest('id')->take(10)->get();
        return response()->json($items);
    }

    // البحث عن الرسائل للزوار بدون أي معرفات
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
                // تشفير مسار PDF للزوار أيضاً
                'pdf_path' => $thesis->pdf_path ? request()->getSchemeAndHttpHost() . '/api/pdf/' . \App\Helpers\PdfPathHelper::encryptPath($thesis->pdf_path) : null,
                'university' => $thesis->university ? $thesis->university->name : null,
                'specialization' => $thesis->specialization ? $thesis->specialization->name : null,
                'degree' => $thesis->degree ? $thesis->degree->name : null,
                'author' => $thesis->author ? $thesis->author->name : null,
            ];
        });
        return response()->json($result->values());
    }

    // Endpoint: /api/pdf/{token}
    public function servePdf($token)
    {
        $realPath = PdfPathHelper::decryptPath($token);
        \Log::info('[PDF DEBUG] realPath after decrypt: ' . print_r($realPath, true));
        if (!$realPath) {
            return response()->json(['message' => 'PDF not found (decrypt error)'], 404);
        }
        // معالجة المسار: حذف أي جزء قبل public/ أو storage/app/public/
        $relative = null;
        if (strpos($realPath, 'storage/app/public/') !== false) {
            $relative = substr($realPath, strpos($realPath, 'storage/app/public/') + strlen('storage/app/public/'));
        } elseif (strpos($realPath, '/storage/') !== false) {
            $relative = ltrim(strstr($realPath, '/storage/'), '/storage/');
        } elseif (strpos($realPath, 'pdfs/') !== false) {
            $relative = substr($realPath, strpos($realPath, 'pdfs/'));
        } elseif (strpos($realPath, 'json_content_new/') !== false) {
            $relative = substr($realPath, strpos($realPath, 'json_content_new/'));
        } else {
            $relative = ltrim($realPath, '/');
        }
        \Log::info('[PDF DEBUG] relative path: ' . $relative);
        $fullPath = storage_path('app/public/' . $relative);
        if (!file_exists($fullPath)) {
            return response()->json(['message' => 'PDF not found (not exists): ' . $relative], 404);
        }
        // إرسال الملف مباشرة مع دعم الكاش
        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=86400', // كاش ليوم كامل
            'Content-Disposition' => 'inline; filename="' . basename($relative) . '"',
        ]);
    }

    // إضافة uuid جديد
    public function storeUuid(Request $request)
    {
        $validated = $request->validate([
            'id_local' => 'required|string|unique:uuids,id_local',
            'id_remote' => 'required|string|unique:uuids,id_remote',
        ]);
        $uuid = \App\Models\Uuid::create($validated);
        return response()->json($uuid, 201);
    }

    // عرض جميع العناصر مع id_local و id_remote فقط
    public function listUuids()
    {
        $items = \App\Models\Uuid::select('id', 'id_local', 'id_remote')->get();
        return response()->json($items);
    }

    // البحث عن عنصر حسب id_local أو id_remote
    public function searchUuid(Request $request)
    {
        $query = \App\Models\Uuid::query();
        if ($request->filled('id_local')) {
            $query->where('id_local', $request->id_local);
        }
        if ($request->filled('id_remote')) {
            $query->where('id_remote', $request->id_remote);
        }
        $items = $query->select('id', 'id_local', 'id_remote')->get();
        return response()->json($items);
    }

    // حذف عنصر حسب id_local أو id_remote
    public function deleteUuid(Request $request)
    {
        $query = \App\Models\Uuid::query();
        if ($request->filled('id_local')) {
            $query->where('id_local', $request->id_local);
        }
        if ($request->filled('id_remote')) {
            $query->where('id_remote', $request->id_remote);
        }
        $count = $query->delete();
        return response()->json(['deleted' => $count > 0, 'count' => $count]);
    }

    // ========== user_uuids endpoints ==========
    // إضافة user_uuid جديد
    public function storeUserUuid(Request $request)
    {
        $validated = $request->validate([
            'id_local' => 'required|string|unique:user_uuids,id_local',
            'id_remote' => 'required|string|unique:user_uuids,id_remote',
        ]);
        $userUuid = \App\Models\UserUuid::create($validated);
        return response()->json($userUuid, 201);
    }

    // عرض جميع العناصر مع id_local و id_remote فقط
    public function listUserUuids()
    {
        $items = \App\Models\UserUuid::select('id', 'id_local', 'id_remote')->get();
        return response()->json($items);
    }

    // البحث عن عنصر حسب id_local أو id_remote
    public function searchUserUuid(Request $request)
    {
        $query = \App\Models\UserUuid::query();
        if ($request->filled('id_local')) {
            $query->where('id_local', $request->id_local);
        }
        if ($request->filled('id_remote')) {
            $query->where('id_remote', $request->id_remote);
        }
        $items = $query->select('id', 'id_local', 'id_remote')->get();
        return response()->json($items);
    }

    // حذف عنصر حسب id_local أو id_remote
    public function deleteUserUuid(Request $request)
    {
        $query = \App\Models\UserUuid::query();
        if ($request->filled('id_local')) {
            $query->where('id_local', $request->id_local);
        }
        if ($request->filled('id_remote')) {
            $query->where('id_remote', $request->id_remote);
        }
        $count = $query->delete();
        return response()->json(['deleted' => $count > 0, 'count' => $count]);
    }

    // جلب رسالة واحدة بالمعرّف
    public function showThesis($id)
    {
        $thesis = Thesis::with(['author', 'university', 'specialization', 'degree'])->findOrFail($id);
        return response()->json([
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
        ]);
    }
}
