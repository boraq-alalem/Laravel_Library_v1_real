<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArchiveThesis;
use App\Helpers\PdfPathHelper;
use Illuminate\Support\Facades\Storage;

class ArchiveThesisController extends Controller
{
    public function getArchivedTheses()
    {
        $theses = ArchiveThesis::with(['author', 'university', 'specialization', 'degree'])->latest('id')->get();
        $result = $theses->map(function($thesis) {
            return [
                'id' => $thesis->id,
                'title' => $thesis->title,
                'year' => $thesis->year,
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
        if ($thesis->pdf_path) {
            $pdfPath = $thesis->pdf_path;
            $dir = dirname($pdfPath);
            $relativeDir = ltrim(str_replace('/storage/', '', $dir), '/');
            Storage::disk('public')->deleteDirectory($relativeDir);
        }
        $thesis->delete();
        return response()->json(['message' => 'تم حذف الرسالة والمجلد نهائياً من الأرشيف']);
    }

    public function restoreThesis($id)
    {
        $archived = ArchiveThesis::findOrFail($id);
        $data = $archived->toArray();
        \App\Models\Thesis::create($data);
        $archived->delete();
        return response()->json(['message' => 'تمت استعادة الرسالة إلى جدول الرسائل بنجاح']);
    }
}
