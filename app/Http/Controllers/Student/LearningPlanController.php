<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LearningPlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = $request->user()->learningPlans()->with('subject')->orderByRaw('target_date IS NULL')->orderBy('target_date')->paginate(10);
        $subjects = $request->user()->subjects()->orderBy('name')->get();
        return view('student.plans.index', compact('plans', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'subject_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target_date' => ['nullable', 'date'],
            'status' => ['required', 'in:planned,in_progress,completed'],
        ]);
        if (!empty($data['subject_id'])) {
            abort_unless($request->user()->subjects()->where('subjects.id', $data['subject_id'])->exists(), 403);
        }
        $request->user()->learningPlans()->create($data);
        return back()->with('success', 'Rencana belajar berhasil ditambahkan.');
    }

    public function update(Request $request, int $plan)
    {
        $item = $request->user()->learningPlans()->findOrFail($plan);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'subject_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target_date' => ['nullable', 'date'],
            'status' => ['required', 'in:planned,in_progress,completed'],
        ]);
        if (!empty($data['subject_id'])) {
            abort_unless($request->user()->subjects()->where('subjects.id', $data['subject_id'])->exists(), 403);
        }
        $item->update($data);
        return back()->with('success', 'Rencana belajar berhasil diperbarui.');
    }

    public function destroy(Request $request, int $plan)
    {
        $request->user()->learningPlans()->findOrFail($plan)->delete();
        return back()->with('success', 'Rencana belajar berhasil dihapus.');
    }
}
