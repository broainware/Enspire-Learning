<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = User::where('role', 'student')->with(['subjects', 'classes'])
            ->when($request->filled('q'), fn ($q) => $q->where(function ($sub) use ($request) {
                $term = $request->input('q');
                $sub->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%");
            }))
            ->when($request->filled('subject_id'), fn ($q) => $q->whereHas('subjects', fn ($s) => $s->where('subjects.id', $request->integer('subject_id'))))
            ->orderBy('name')->paginate(10)->withQueryString();

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $classes = ClassRoom::where('is_active', true)->orderBy('name')->get();
        return view('teacher.students.index', compact('students', 'subjects', 'classes'));
    }

    /** Link an already provisioned student account to a subject/class. No new login is created here. */
    public function assign(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
        ]);

        $student = User::where('email', $data['email'])->where('role', 'student')->first();
        if (!$student) {
            return back()->withErrors(['email' => 'Akun dengan email tersebut tidak ditemukan sebagai akun siswa.'])->withInput();
        }

        $student->subjects()->syncWithoutDetaching([
            $data['subject_id'] => ['assigned_at' => now()],
        ]);

        if (!empty($data['class_id'])) {
            $student->classes()->syncWithoutDetaching([
                $data['class_id'] => ['joined_at' => now()],
            ]);
        }

        return back()->with('success', 'Akun siswa berhasil dihubungkan ke mata pelajaran/kelas. Tidak ada akun baru yang dibuat.');
    }

    public function detach(User $student, Subject $subject)
    {
        abort_unless($student->role === 'student', 404);
        $student->subjects()->detach($subject->id);
        return back()->with('success', 'Mata pelajaran berhasil dilepaskan dari akun siswa.');
    }
}
