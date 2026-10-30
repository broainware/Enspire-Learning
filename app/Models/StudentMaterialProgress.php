<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMaterialProgress extends Model
{
    protected $table = 'student_material_progress';
    protected $fillable = ['student_id', 'material_id', 'status', 'progress_percent', 'last_accessed_at', 'completed_at'];

    protected function casts(): array
    {
        return [
            'last_accessed_at' => 'datetime',
            'completed_at' => 'datetime',
            'progress_percent' => 'integer',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
