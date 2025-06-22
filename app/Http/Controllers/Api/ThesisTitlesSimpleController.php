<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThesisTitlesSimple;

class ThesisTitlesSimpleController extends Controller
{
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
    public function latestThesisTitlesSimple()
    {
        $items = ThesisTitlesSimple::select('id', 'title', 'person_name', 'university')->latest('id')->take(10)->get();
        return response()->json($items);
    }
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
}
