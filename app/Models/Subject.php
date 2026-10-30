<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_subjects', 'subject_id', 'student_id')->withPivot('assigned_at')->withTimestamps();
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function teachingSessions()
    {
        return $this->hasMany(TeachingSession::class);
    }
}
