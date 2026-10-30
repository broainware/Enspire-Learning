<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\Subject;
use App\Models\TeachingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role).' Test', 'email' => $email,
            'password' => Hash::make('secret123'), 'role' => $role, 'is_active' => true,
        ]);
    }

    public function test_student_cannot_open_teacher_report(): void
    {
        $student = $this->makeUser('student', 'student@example.test');
        $this->actingAs($student)->get('/teacher/reports')->assertForbidden();
    }

    public function test_teacher_cannot_open_student_progress_area(): void
    {
        $teacher = $this->makeUser('teacher', 'teacher@example.test');
        $this->actingAs($teacher)->get('/student/progress')->assertForbidden();
    }

    public function test_student_cannot_open_material_from_unassigned_subject(): void
    {
        $teacher = $this->makeUser('teacher', 'teacher@example.test');
        $student = $this->makeUser('student', 'student@example.test');
        $math = Subject::create(['name' => 'Matematika', 'slug' => 'matematika-test', 'is_active' => true]);
        $physics = Subject::create(['name' => 'Fisika', 'slug' => 'fisika-test', 'is_active' => true]);
        $student->subjects()->attach($math->id);
        $material = Material::create([
            'subject_id' => $physics->id, 'title' => 'Materi Fisika Privat',
            'status' => 'published', 'created_by' => $teacher->id,
        ]);

        $this->actingAs($student)->get('/student/materials/'.$material->id)->assertForbidden();
    }

    public function test_teacher_cannot_edit_another_teachers_session(): void
    {
        $owner = $this->makeUser('teacher', 'owner@example.test');
        $other = $this->makeUser('teacher', 'other@example.test');
        $subject = Subject::create(['name' => 'Matematika', 'slug' => 'matematika', 'is_active' => true]);
        $session = TeachingSession::create([
            'teacher_id' => $owner->id, 'subject_id' => $subject->id,
            'session_date' => now()->toDateString(), 'start_time' => '09:00', 'end_time' => '10:00',
            'status' => 'scheduled', 'created_by' => $owner->id,
        ]);

        $this->actingAs($other)->get('/teacher/schedules/'.$session->id.'/edit')->assertForbidden();
    }
}
