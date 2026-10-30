<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;

    protected $fillable = ['subject_id', 'title', 'description', 'education_level', 'category', 'status', 'created_by'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->hasMany(MaterialFile::class);
    }

    public function teachingSessions()
    {
        return $this->hasMany(TeachingSession::class);
    }

    public function progressRecords()
    {
        return $this->hasMany(StudentMaterialProgress::class);
    }
}
