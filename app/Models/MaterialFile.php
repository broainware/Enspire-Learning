<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialFile extends Model
{
    protected $fillable = ['material_id', 'original_name', 'storage_path', 'mime_type', 'size_bytes', 'uploaded_by'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
