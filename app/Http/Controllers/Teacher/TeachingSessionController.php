<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Material;
use App\Models\Subject;
use App\Models\TeachingSession;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TeachingSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = TeachingSession::with(['subject', 'classRoom', 'material'])
            ->where('teacher_id', $request->user()->id)
            ->when($request->filled('month'), fn ($q) => $q->whereMonth('session_date', $request->integer('month')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->orderByDesc('session_date')->orderBy('start_time')->paginate(10)->withQueryString();
        return view('teacher.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $session = new TeachingSession();
        return view('teacher.sessions.form', $this->formData($session));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->ensureNoConflict($data);
        $data['teacher_id'] = $request->user()->id;
        $data['created_by'] = $request->user()->id;
        TeachingSession::create($data);
        return redirect()->route('teacher.sessions.index')->with('success', 'Jadwal mengajar berhasil ditambahkan.');
    }

    public function edit(TeachingSession $session)
    {
        abort_unless($session->teacher_id === auth()->id(), 403);
        return view('teacher.sessions.form', $this->formData($session));
    }

    public function update(Request $request, TeachingSession $session)
    {
        abort_unless($session->teacher_id === $request->user()->id, 403);
        $data = $this->validated($request);
        $this->ensureNoConflict($data, $session->id);
        $session->update($data);
        return redirect()->route('teacher.sessions.index')->with('success', 'Jadwal mengajar berhasil diperbarui.');
    }

    public function destroy(Request $request, TeachingSession $session)
    {
        abort_unless($session->teacher_id === $request->user()->id, 403);
        $session->delete();
        return redirect()->route('teacher.sessions.index')->with('success', 'Jadwal mengajar berhasil dihapus.');
    }

    private function formData(TeachingSession $session): array
    {
        return [
            'session' => $session,
            'subjects' => Subject::where('is_active', true)->orderBy('name')->get(),
            'classes' => ClassRoom::where('is_active', true)->orderBy('name')->get(),
            'materials' => Material::with('subject')->orderBy('title')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'material_id' => ['nullable', 'exists:materials,id'],
            'session_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'student_count' => ['nullable', 'integer', 'min:0', 'max:500'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        if (!empty($data['material_id'])) {
            $material = Material::findOrFail($data['material_id']);
            if ((int) $material->subject_id !== (int) $data['subject_id']) {
                throw ValidationException::withMessages(['material_id' => 'Materi harus berasal dari mata pelajaran yang dipilih.']);
            }
        }

        return $data;
    }

    private function ensureNoConflict(array $data, ?int $exceptId = null): void
    {
        if ($data['status'] === 'cancelled') return;

        $query = TeachingSession::where('teacher_id', auth()->id())
            ->whereDate('session_date', $data['session_date'])
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time']);

        if ($exceptId) $query->where('id', '!=', $exceptId);

        if ($query->exists()) {
            throw ValidationException::withMessages(['start_time' => 'Jadwal bentrok dengan sesi mengajar lain pada waktu tersebut.']);
        }
    }
}
