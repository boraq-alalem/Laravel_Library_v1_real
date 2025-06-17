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
use Illuminate\Support\Facades\Storage;

class StatsController extends Controller
{
    public function index()
    {
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
    }

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
                // إرجاع pdf_path كما هو من قاعدة البيانات
                'pdf_path' => $thesis->pdf_path ?: null,
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
                // إرجاع pdf_path كما هو من قاعدة البيانات
                'pdf_path' => $thesis->pdf_path ?: null,
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
        // إذا تم رفع ملف PDF جديد
        if ($request->hasFile('pdf')) {
            // حذف ملف PDF القديم إذا كان موجوداً
            if ($thesis->pdf_path) {
                $oldPath = str_replace('/storage/', '', $thesis->pdf_path);
                \Storage::disk('public')->delete($oldPath);
            }
            // حفظ الملف الجديد
            $pdfFile = $request->file('pdf');
            $pdfName = $pdfFile->getClientOriginalName();
            // إعادة استخدام نفس منطق المسار كما في storeThesis
            $author = $thesis->author;
            $degree = $thesis->degree;
            $specialization = $thesis->specialization;
            $basePath = 'pdfs/json_content';
            $degreeFolder = preg_replace('/\s+/u', '_', $degree ? $degree->name : 'بدون_درجة');
            $specializationFolder = preg_replace('/\s+/u', '_', $specialization ? $specialization->name : 'بدون_تخصص');
            $authorFolder = preg_replace('/\s+/u', '_', $author ? $author->name : 'بدون_اسم');
            $targetDir = "$basePath/$degreeFolder/$specializationFolder/$authorFolder";
            $relativePath = "$targetDir/$pdfName";
            $pdfPath = $pdfFile->storeAs($targetDir, $pdfName, 'public');
            $requestData = $request->only([
                'title', 'year', 'university_id', 'specialization_id', 'degree_id', 'author_id'
            ]);
            $requestData['pdf_path'] = '/storage/' . $relativePath;
            $thesis->update($requestData);
        } else {
            $thesis->update($request->only([
                'title', 'year', 'pdf_path', 'university_id', 'specialization_id', 'degree_id', 'author_id'
            ]));
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
                'pdf_path' => $thesis->pdf_path ?: null,
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
        $query = University::with('specializations:id,name');
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

        // إنشاء أو جلب الباحث
        $author = Author::firstOrCreate(['name' => $validated['author_name']]);

        // جلب أسماء الدرجة والتخصص والجامعة
        $degree = Degree::find($validated['degree_id']);
        $specialization = Specialization::find($validated['specialization_id']);
        $university = University::find($validated['university_id']);

        // تجهيز المسار المطلوب
        $basePath = 'pdfs/json_content';
        $degreeName = $degree ? $degree->name : 'بدون_درجة';
        $specializationName = $specialization ? $specialization->name : 'بدون_تخصص';
        $authorName = $author->name;
        // transliterate/replace spaces for folder names
        $degreeFolder = preg_replace('/\s+/u', '_', $degreeName);
        $specializationFolder = preg_replace('/\s+/u', '_', $specializationName);
        $authorFolder = preg_replace('/\s+/u', '_', $authorName);
        $targetDir = "$basePath/$degreeFolder/$specializationFolder/$authorFolder";

        // حفظ ملف PDF في المسار الجديد
        $pdfFile = $request->file('pdf');
        $pdfName = $pdfFile->getClientOriginalName();
        $relativePath = "$targetDir/$pdfName";
        $pdfPath = $pdfFile->storeAs($targetDir, $pdfName, 'public');

        // حفظ المسار في قاعدة البيانات مع /storage/ في البداية
        $dbPdfPath = '/storage/' . $relativePath;

        // إنشاء الرسالة في قاعدة البيانات
        $thesis = Thesis::create([
            'title' => $validated['title'],
            'year' => $validated['year'],
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
                'pdf_path' => $thesis->pdf_path ?: null,
                'university' => $thesis->university ? $thesis->university->name : null,
                'specialization' => $thesis->specialization ? $thesis->specialization->name : null,
                'degree' => $thesis->degree ? $thesis->degree->name : null,
                'author' => $thesis->author ? $thesis->author->name : null,
            ];
        });
        return response()->json($result->values());
    }
}
