<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TeachingSession;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $subjectIds = $request->user()->subjects()->pluck('subjects.id');
        $classIds = $request->user()->classes()->pluck('classes.id');
        $query = TeachingSession::with(['subject', 'teacher', 'classRoom', 'material'])
            ->whereIn('subject_id', $subjectIds)->whereNotIn('status', ['cancelled']);
        if ($classIds->isNotEmpty()) {
            $query->where(function ($sub) use ($classIds) { $sub->whereIn('class_id', $classIds)->orWhereNull('class_id'); });
        }
        $sessions = $query->when($request->filled('month'), fn ($q) => $q->whereMonth('session_date', $request->integer('month')))
            ->orderBy('session_date')->orderBy('start_time')->paginate(12)->withQueryString();
        return view('student.schedules.index', compact('sessions'));
    }
}
