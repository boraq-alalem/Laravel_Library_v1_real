<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Degree;

class DegreeController extends Controller
{
    public function allDegrees()
    {
        return response()->json(Degree::all());
    }
}
