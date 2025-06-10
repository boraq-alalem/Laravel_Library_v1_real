<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Thesis;
use App\Models\Author;
use App\Models\University;
use App\Models\Degree;
use App\Models\Specialization;

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
        return response()->json($theses);
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
        // إعادة تنسيق النتائج بدون created_at و updated_at
        $result = $theses->map(function($thesis) {
            return [
                'id' => $thesis->id,
                'title' => $thesis->title,
                'year' => $thesis->year,
                'pdf_path' => $thesis->pdf_path,
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
        $thesis->update($request->only([
            'title', 'year', 'pdf_path', 'university_id', 'specialization_id', 'degree_id', 'author_id'
        ]));
        return response()->json(['message' => 'تم التعديل بنجاح', 'thesis' => $thesis->fresh()]);
    }

    public function deleteThesis($id)
    {
        $thesis = Thesis::findOrFail($id);
        $thesis->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
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

        // رفع ملف PDF
        $pdfPath = $request->file('pdf')->store('theses', 'public');

        // إنشاء الرسالة
        $thesis = Thesis::create([
            'title' => $validated['title'],
            'year' => $validated['year'],
            'pdf_path' => $pdfPath,
            'university_id' => $validated['university_id'],
            'specialization_id' => $validated['specialization_id'],
            'degree_id' => $validated['degree_id'],
            'author_id' => $author->id,
        ]);

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
}
