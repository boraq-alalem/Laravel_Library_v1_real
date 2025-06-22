<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialization;

class SpecializationController extends Controller
{
    public function allSpecializations()
    {
        return response()->json(Specialization::all());
    }
}
