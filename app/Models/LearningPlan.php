<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningPlan extends Model
{
    use SoftDeletes;

    protected $fillable = ['student_id', 'subject_id', 'title', 'description', 'target_date', 'status'];

    protected function casts(): array
    {
        return ['target_date' => 'date'];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
