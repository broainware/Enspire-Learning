<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeachingSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'teacher_id', 'subject_id', 'class_id', 'material_id', 'session_date',
        'start_time', 'end_time', 'student_count', 'status', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return ['session_date' => 'date'];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'completed') return 'Selesai';
        if ($this->status === 'in_progress') return 'Berlangsung';
        if ($this->status === 'cancelled') return 'Dibatalkan';
        if ($this->session_date->isPast() && !$this->session_date->isToday()) return 'Perlu Perhatian';
        return 'Mendatang';
    }
}
