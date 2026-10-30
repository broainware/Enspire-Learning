@extends('layouts.app')
@section('title', $material->exists ? 'Edit Materi' : 'Tambah Materi')
@section('content')
<div class="page-heading"><div><p class="eyebrow">MANAJEMEN MATERI</p><h1>{{ $material->exists ? 'Edit Materi' : 'Tambah Materi Baru' }}</h1><p>Lengkapi informasi materi agar mudah ditemukan dan dipelajari siswa.</p></div><a href="{{ route('teacher.materials.index') }}" class="btn btn-secondary">← Kembali</a></div>
<section class="panel form-panel"><form method="POST" enctype="multipart/form-data" action="{{ $material->exists ? route('teacher.materials.update',$material) : route('teacher.materials.store') }}">@csrf @if($material->exists) @method('PUT') @endif
    <div class="form-grid"><div class="field-group field-span-2"><label>Nama/Judul Materi <b>*</b></label><input class="form-control" name="title" value="{{ old('title',$material->title) }}" placeholder="Contoh: Persamaan Linear dan Kuadrat" required></div>
    <div class="field-group"><label>Mata Pelajaran <b>*</b></label><select class="form-control" name="subject_id" required><option value="">Pilih mata pelajaran</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(old('subject_id',$material->subject_id)==$subject->id)>{{ $subject->name }}</option>@endforeach</select></div>
    <div class="field-group"><label>Jenjang/Kelas</label><input class="form-control" name="education_level" value="{{ old('education_level',$material->education_level) }}" placeholder="Contoh: SMA 10"></div>
    <div class="field-group"><label>Kategori</label><input class="form-control" name="category" value="{{ old('category',$material->category) }}" placeholder="Materi Pembelajaran / Latihan Soal"></div>
    <div class="field-group"><label>Status Publikasi <b>*</b></label><select class="form-control" name="status"><option value="draft" @selected(old('status',$material->status ?: 'draft')==='draft')>Draft</option><option value="published" @selected(old('status',$material->status)==='published')>Dipublikasikan</option></select></div>
    <div class="field-group field-span-2"><label>Deskripsi Materi</label><textarea class="form-control" rows="5" name="description" placeholder="Tuliskan ringkasan materi...">{{ old('description',$material->description) }}</textarea></div>
    <div class="field-group field-span-2"><label>Lampiran Materi</label><input class="form-control file-control" type="file" name="files[]" multiple accept=".pdf,.ppt,.pptx,.doc,.docx,.jpg,.jpeg,.png"><small class="form-help">Format: PDF, PPT/PPTX, DOC/DOCX, JPG, PNG. Maksimal 10 MB per file.</small></div>
    @if($material->exists && $material->files->count())<div class="field-group field-span-2"><label>Lampiran saat ini</label><div class="attachment-list">@foreach($material->files as $file)<div class="attachment-item">▧ {{ $file->original_name }} <small>{{ number_format($file->size_bytes/1024,0) }} KB</small></div>@endforeach</div></div>@endif
    </div>
    <div class="form-actions"><a href="{{ route('teacher.materials.index') }}" class="btn btn-secondary">Batal</a><button class="btn btn-primary" type="submit">{{ $material->exists ? 'Simpan Perubahan' : 'Simpan Materi' }} →</button></div>
</form></section>
@endsection
