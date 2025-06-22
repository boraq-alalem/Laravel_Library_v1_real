<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\University;
use App\Models\Specialization;

class UniversityController extends Controller
{
    public function allUniversities()
    {
        return response()->json(University::all());
    }

    public function universitiesWithSpecializations()
    {
        $universities = University::with('specializations')->get();
        return response()->json($universities);
    }

    public function universitiesWithSpecializationsForGuests()
    {
        $universities = University::with('specializations')->get();
        return response()->json($universities);
    }

    public function searchUniversities(Request $request)
    {
        $q = $request->input('q');
        $universities = University::when($q, function($query) use ($q) {
            $query->where('name', 'like', "%$q%");
        })->get();
        return response()->json($universities);
    }

    public function addSpecializationToUniversity(Request $request, University $university)
    {
        $request->validate([
            'specialization_id' => 'required|exists:specializations,id',
        ]);
        $university->specializations()->syncWithoutDetaching([$request->specialization_id]);
        return response()->json(['message' => 'تمت إضافة التخصص للجامعة بنجاح']);
    }
}
