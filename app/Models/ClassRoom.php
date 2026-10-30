<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $table = 'classes';
    protected $fillable = ['name', 'education_level', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_students', 'class_id', 'student_id')->withPivot('joined_at')->withTimestamps();
    }

    public function teachingSessions()
    {
        return $this->hasMany(TeachingSession::class, 'class_id');
    }
}
