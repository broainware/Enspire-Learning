<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialFile;
use App\Models\StudentMaterialProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();
        $subjectIds = $student->subjects()->pluck('subjects.id');
        $materials = Material::with(['subject', 'files', 'progressRecords' => fn ($q) => $q->where('student_id', $student->id)])
            ->whereIn('subject_id', $subjectIds)->where('status', 'published')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->input('q').'%'))
            ->when($request->filled('subject_id') && $subjectIds->contains((int) $request->input('subject_id')), fn ($q) => $q->where('subject_id', $request->integer('subject_id')))
            ->latest()->paginate(9)->withQueryString();
        $subjects = $student->subjects()->orderBy('name')->get();
        return view('student.materials.index', compact('materials', 'subjects'));
    }

    public function show(Request $request, Material $material)
    {
        $this->authorizeMaterial($request, $material);
        $progress = StudentMaterialProgress::firstOrCreate(
            ['student_id' => $request->user()->id, 'material_id' => $material->id],
            ['status' => 'in_progress', 'progress_percent' => 10, 'last_accessed_at' => now()]
        );
        $progress->update(['last_accessed_at' => now()]);
        $material->load(['subject', 'files']);
        return view('student.materials.show', compact('material', 'progress'));
    }

    public function download(Request $request, MaterialFile $file)
    {
        $file->load('material');
        $this->authorizeMaterial($request, $file->material);
        abort_unless(Storage::disk('local')->exists($file->storage_path), 404);
        return Storage::disk('local')->download($file->storage_path, $file->original_name);
    }

    private function authorizeMaterial(Request $request, Material $material): void
    {
        abort_unless($material->status === 'published', 404);
        $allowed = $request->user()->subjects()->where('subjects.id', $material->subject_id)->exists();
        abort_unless($allowed, 403, 'Materi ini bukan bagian dari mata pelajaran yang kamu ikuti.');
    }
}
