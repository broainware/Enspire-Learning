<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\LearningPlan;
use App\Models\Material;
use App\Models\Subject;
use App\Models\StudentMaterialProgress;
use App\Models\TeachingSession;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $math = Subject::updateOrCreate(['slug' => 'matematika'], ['name' => 'Matematika', 'description' => 'Mata pelajaran Matematika', 'is_active' => true]);
        $physics = Subject::updateOrCreate(['slug' => 'fisika'], ['name' => 'Fisika', 'description' => 'Mata pelajaran Fisika', 'is_active' => true]);
        $chemistry = Subject::updateOrCreate(['slug' => 'kimia'], ['name' => 'Kimia', 'description' => 'Mata pelajaran Kimia', 'is_active' => true]);
        $english = Subject::updateOrCreate(['slug' => 'bahasa-inggris'], ['name' => 'Bahasa Inggris', 'description' => 'Mata pelajaran Bahasa Inggris', 'is_active' => true]);

        $classSma10 = ClassRoom::firstOrCreate(['name' => 'SMA 10'], ['education_level' => 'SMA', 'is_active' => true]);
        $classSmp8 = ClassRoom::firstOrCreate(['name' => 'SMP 8'], ['education_level' => 'SMP', 'is_active' => true]);
        $classSma12 = ClassRoom::firstOrCreate(['name' => 'SMA 12'], ['education_level' => 'SMA', 'is_active' => true]);

        $teacher = User::updateOrCreate(['email' => 'ayu@enspire.test'], [
            'name' => 'Ayu Lestari', 'password' => Hash::make('AyuEnspire123!'), 'role' => 'teacher',
            'phone' => '081234567890', 'is_active' => true,
        ]);
        $teacherTwo = User::updateOrCreate(['email' => 'aulia@enspire.test'], [
            'name' => 'Aulia Rahayu', 'password' => Hash::make('AuliaEnspire123!'), 'role' => 'teacher',
            'phone' => '081298765432', 'is_active' => true,
        ]);
        $studentMath = User::updateOrCreate(['email' => 'zahra@enspire.test'], [
            'name' => 'Zahra Nurmala', 'password' => Hash::make('Enspire123!'), 'role' => 'student',
            'phone' => '081300000001', 'is_active' => true,
        ]);
        $studentPhysics = User::updateOrCreate(['email' => 'andi@enspire.test'], [
            'name' => 'Andi Pratama', 'password' => Hash::make('AndiEnspire123!'), 'role' => 'student',
            'phone' => '081300000002', 'is_active' => true,
        ]);

        $studentMath->subjects()->syncWithoutDetaching([$math->id => ['assigned_at' => now()]]);
        $studentMath->classes()->syncWithoutDetaching([$classSma10->id => ['joined_at' => now()]]);
        $studentPhysics->subjects()->syncWithoutDetaching([$physics->id => ['assigned_at' => now()]]);
        $studentPhysics->classes()->syncWithoutDetaching([$classSmp8->id => ['joined_at' => now()]]);

        $materials = [];
        $materials[] = Material::updateOrCreate(['title' => 'Persamaan Linear & Kuadrat'], [
            'subject_id' => $math->id, 'description' => 'Memahami persamaan linear, persamaan kuadrat, dan latihan penerapannya dalam soal.',
            'education_level' => 'SMA 10', 'category' => 'Materi Pembelajaran', 'status' => 'published', 'created_by' => $teacher->id,
        ]);
        $materials[] = Material::updateOrCreate(['title' => 'Trigonometri Dasar'], [
            'subject_id' => $math->id, 'description' => 'Pengantar perbandingan trigonometri, sudut istimewa, dan penerapannya.',
            'education_level' => 'SMA 10', 'category' => 'Materi Pembelajaran', 'status' => 'published', 'created_by' => $teacher->id,
        ]);
        $materials[] = Material::updateOrCreate(['title' => 'Gerak Lurus Beraturan (GLB)'], [
            'subject_id' => $physics->id, 'description' => 'Konsep dasar GLB dan GLBB serta penerapannya dalam kehidupan sehari-hari.',
            'education_level' => 'SMP 8', 'category' => 'Latihan Soal', 'status' => 'published', 'created_by' => $teacherTwo->id,
        ]);
        $materials[] = Material::updateOrCreate(['title' => 'Stoikiometri & Larutan'], [
            'subject_id' => $chemistry->id, 'description' => 'Konsep mol, persamaan reaksi, dan perhitungan konsentrasi larutan.',
            'education_level' => 'SMA 12', 'category' => 'Persiapan UTBK/SNBT', 'status' => 'published', 'created_by' => $teacher->id,
        ]);
        Material::updateOrCreate(['title' => 'Reading Comprehension'], [
            'subject_id' => $english->id, 'description' => 'Strategi membaca teks bahasa Inggris dan memahami gagasan utama.',
            'education_level' => 'SMA 10', 'category' => 'Materi Pembelajaran', 'status' => 'draft', 'created_by' => $teacher->id,
        ]);

        $sessionData = [
            [$teacher->id, $math->id, $classSma10->id, $materials[0]->id, now()->subDays(2)->toDateString(), '09:00:00', '10:30:00', 12, 'completed', 'Pembahasan latihan persamaan kuadrat.'],
            [$teacher->id, $math->id, $classSma10->id, $materials[1]->id, today()->toDateString(), '16:00:00', '17:30:00', 12, 'scheduled', 'Pembahasan sudut istimewa.'],
            [$teacher->id, $math->id, $classSma10->id, $materials[1]->id, now()->addDays(2)->toDateString(), '16:00:00', '17:30:00', 12, 'scheduled', 'Latihan trigonometri.'],
            [$teacher->id, $chemistry->id, $classSma12->id, $materials[3]->id, now()->subDays(5)->toDateString(), '13:00:00', '14:30:00', 10, 'completed', 'Pengenalan konsep mol.'],
            [$teacher->id, $math->id, $classSma10->id, $materials[0]->id, now()->subDays(1)->toDateString(), '10:00:00', '11:00:00', 12, 'scheduled', 'Perlu diperbarui setelah pertemuan.'],
            [$teacherTwo->id, $physics->id, $classSmp8->id, $materials[2]->id, now()->addDays(1)->toDateString(), '10:00:00', '11:30:00', 9, 'scheduled', 'Materi gerak lurus.'],
        ];

        foreach ($sessionData as [$teacherId, $subjectId, $classId, $materialId, $date, $start, $end, $count, $status, $notes]) {
            TeachingSession::firstOrCreate([
                'teacher_id' => $teacherId, 'session_date' => $date, 'start_time' => $start,
            ], [
                'subject_id' => $subjectId, 'class_id' => $classId, 'material_id' => $materialId,
                'end_time' => $end, 'student_count' => $count, 'status' => $status,
                'notes' => $notes, 'created_by' => $teacherId,
            ]);
        }

        LearningPlan::firstOrCreate(['student_id' => $studentMath->id, 'title' => 'Mengulang materi Trigonometri'], [
            'subject_id' => $math->id, 'description' => 'Mengerjakan ulang latihan sudut istimewa.',
            'target_date' => now()->addDays(4)->toDateString(), 'status' => 'in_progress',
        ]);
        LearningPlan::firstOrCreate(['student_id' => $studentMath->id, 'title' => 'Mengerjakan latihan persamaan kuadrat'], [
            'subject_id' => $math->id, 'description' => 'Selesaikan minimal 10 soal latihan.',
            'target_date' => now()->addDays(7)->toDateString(), 'status' => 'planned',
        ]);

        StudentMaterialProgress::updateOrCreate(['student_id' => $studentMath->id, 'material_id' => $materials[0]->id], [
            'status' => 'completed', 'progress_percent' => 100, 'last_accessed_at' => now()->subDay(), 'completed_at' => now()->subDay(),
        ]);
        StudentMaterialProgress::updateOrCreate(['student_id' => $studentMath->id, 'material_id' => $materials[1]->id], [
            'status' => 'in_progress', 'progress_percent' => 50, 'last_accessed_at' => now(), 'completed_at' => null,
        ]);
        StudentMaterialProgress::updateOrCreate(['student_id' => $studentPhysics->id, 'material_id' => $materials[2]->id], [
            'status' => 'not_started', 'progress_percent' => 0, 'last_accessed_at' => null, 'completed_at' => null,
        ]);
    }
}
