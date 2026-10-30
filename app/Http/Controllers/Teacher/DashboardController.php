<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\TeachingSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        $today = now()->toDateString();
        $todaySessions = TeachingSession::with(['subject', 'classRoom', 'material', 'teacher'])
            ->where('teacher_id', $teacher->id)->whereDate('session_date', $today)
            ->orderBy('start_time')->get();

        $totalStudents = User::where('role', 'student')->where('is_active', true)
            ->whereHas('subjects', function ($query) use ($teacher) {
                $subjectIds = TeachingSession::where('teacher_id', $teacher->id)->select('subject_id')->distinct();
                $query->whereIn('subjects.id', $subjectIds);
            })->count();

        $materialCount = Material::count();
        $todayCount = $todaySessions->count();
        $attentionCount = TeachingSession::where('teacher_id', $teacher->id)
            ->whereDate('session_date', '<', $today)
            ->whereNotIn('status', ['completed', 'cancelled'])->count();

        $weekLabels = [];
        $completedSeries = [];
        $pendingSeries = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $weekLabels[] = $day->translatedFormat('D');
            $base = TeachingSession::where('teacher_id', $teacher->id)->whereDate('session_date', $day->toDateString());
            $completedSeries[] = (clone $base)->where('status', 'completed')->count();
            $pendingSeries[] = (clone $base)->whereNotIn('status', ['completed', 'cancelled'])->count();
        }

        $latestMaterials = Material::with(['subject', 'createdBy'])->latest()->take(4)->get();
        $calendarSessions = TeachingSession::where('teacher_id', $teacher->id)
            ->whereYear('session_date', now()->year)->whereMonth('session_date', now()->month)->get();
        $eventDates = $calendarSessions->pluck('session_date')->map(fn ($date) => $date->format('Y-m-d'))->unique()->values();
        $calendarStartOffset = now()->startOfMonth()->dayOfWeek;
        $calendarDays = range(1, now()->daysInMonth);

        return view('teacher.dashboard.index', compact(
            'teacher', 'todaySessions', 'totalStudents', 'materialCount', 'todayCount', 'attentionCount',
            'weekLabels', 'completedSeries', 'pendingSeries', 'latestMaterials', 'eventDates',
            'calendarStartOffset', 'calendarDays'
        ));
    }
}
