<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}
