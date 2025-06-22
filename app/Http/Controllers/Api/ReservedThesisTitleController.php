<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReservedThesisTitle;

class ReservedThesisTitleController extends Controller
{
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
    public function latestReservedThesisTitles()
    {
        $items = ReservedThesisTitle::select('id', 'title', 'person_name', 'university', 'specialization', 'degree', 'date')
            ->latest('id')->take(10)->get();
        return response()->json($items);
    }
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
    public function deleteReservedThesisTitle($id)
    {
        $item = ReservedThesisTitle::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
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
    public function latestReservedThesisTitlesForGuests()
    {
        $items = ReservedThesisTitle::select('title', 'person_name', 'university')
            ->latest('id')->take(10)->get();
        return response()->json($items);
    }
}
