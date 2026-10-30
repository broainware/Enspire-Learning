<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'photo_path', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subjects', 'student_id', 'subject_id')->withPivot('assigned_at')->withTimestamps();
    }

    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_students', 'student_id', 'class_id')->withPivot('joined_at')->withTimestamps();
    }

    public function createdMaterials()
    {
        return $this->hasMany(Material::class, 'created_by');
    }

    public function teachingSessions()
    {
        return $this->hasMany(TeachingSession::class, 'teacher_id');
    }

    public function createdSessions()
    {
        return $this->hasMany(TeachingSession::class, 'created_by');
    }

    public function learningPlans()
    {
        return $this->hasMany(LearningPlan::class, 'student_id');
    }

    public function materialProgress()
    {
        return $this->hasMany(StudentMaterialProgress::class, 'student_id');
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}
