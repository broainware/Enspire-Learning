<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\TeachingSession;
use App\Models\StudentMaterialProgress;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();
        $subjectIds = $student->subjects()->pluck('subjects.id');
        $classIds = $student->classes()->pluck('classes.id');
        $materialsQuery = Material::with(['subject', 'files'])->whereIn('subject_id', $subjectIds)->where('status', 'published');
        $materialCount = (clone $materialsQuery)->count();
        $latestMaterials = (clone $materialsQuery)->latest()->take(4)->get();

        $sessionsQuery = TeachingSession::with(['subject', 'teacher', 'classRoom', 'material'])
            ->whereIn('subject_id', $subjectIds)
            ->whereNotIn('status', ['cancelled']);
        if ($classIds->isNotEmpty()) {
            $sessionsQuery->where(function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds)->orWhereNull('class_id');
            });
        }
        $sessions = (clone $sessionsQuery)->whereDate('session_date', '>=', today())->orderBy('session_date')->orderBy('start_time')->take(5)->get();

        $totalProgress = StudentMaterialProgress::where('student_id', $student->id)->whereIn('material_id', $materialsQuery->select('id'))->count();
        $doneProgress = StudentMaterialProgress::where('student_id', $student->id)->where('status', 'completed')->whereIn('material_id', $materialsQuery->select('id'))->count();
        $progressPercent = $materialCount > 0 ? (int) round(($doneProgress / $materialCount) * 100) : 0;

        $calendarSessions = (clone $sessionsQuery)->whereYear('session_date', now()->year)->whereMonth('session_date', now()->month)->get();
        $eventDates = $calendarSessions->pluck('session_date')->map(fn ($date) => $date->format('Y-m-d'))->unique()->values();
        $calendarStartOffset = now()->startOfMonth()->dayOfWeek;
        $calendarDays = range(1, now()->daysInMonth);

        return view('student.dashboard.index', compact(
            'student', 'subjectIds', 'materialCount', 'latestMaterials', 'sessions',
            'progressPercent', 'doneProgress', 'eventDates', 'calendarStartOffset', 'calendarDays'
        ));
    }
}
