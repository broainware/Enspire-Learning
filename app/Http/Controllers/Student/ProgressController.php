<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\StudentMaterialProgress;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $subjectIds = $request->user()->subjects()->pluck('subjects.id');
        $materials = Material::with(['subject', 'progressRecords' => fn ($q) => $q->where('student_id', $request->user()->id)])
            ->whereIn('subject_id', $subjectIds)->where('status', 'published')->latest()->paginate(10);
        $total = Material::whereIn('subject_id', $subjectIds)->where('status', 'published')->count();
        $done = StudentMaterialProgress::where('student_id', $request->user()->id)->where('status', 'completed')->whereHas('material', fn ($q) => $q->whereIn('subject_id', $subjectIds))->count();
        $overall = $total ? (int) round($done / $total * 100) : 0;
        return view('student.progress.index', compact('materials', 'total', 'done', 'overall'));
    }

    public function update(Request $request, Material $material)
    {
        $student = $request->user();
        abort_unless($material->status === 'published' && $student->subjects()->where('subjects.id', $material->subject_id)->exists(), 403);
        $data = $request->validate(['status' => ['required', 'in:not_started,in_progress,completed']]);
        $percent = match ($data['status']) {
            'completed' => 100,
            'in_progress' => 50,
            default => 0,
        };
        StudentMaterialProgress::updateOrCreate(
            ['student_id' => $student->id, 'material_id' => $material->id],
            ['status' => $data['status'], 'progress_percent' => $percent,
             'last_accessed_at' => now(), 'completed_at' => $data['status'] === 'completed' ? now() : null]
        );
        return back()->with('success', 'Progress belajar berhasil diperbarui.');
    }
}
