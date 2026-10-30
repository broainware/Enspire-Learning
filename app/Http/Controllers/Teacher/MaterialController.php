<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialFile;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $materials = Material::with(['subject', 'createdBy', 'files'])->when($request->filled('q'), function ($query) use ($request) {
            $term = $request->string('q');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%");
            });
        })->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->integer('subject_id')))
          ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
          ->latest()->paginate(8)->withQueryString();

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        return view('teacher.materials.index', compact('materials', 'subjects'));
    }

    public function create()
    {
        $material = new Material();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        return view('teacher.materials.form', compact('material', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'education_level' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,published'],
            'files.*' => ['nullable', 'file', 'mimes:pdf,ppt,pptx,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $data['created_by'] = $request->user()->id;
        $material = Material::create($data);
        $this->storeFiles($request, $material);

        return redirect()->route('teacher.materials.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Material $material)
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $material->load('files');
        return view('teacher.materials.form', compact('material', 'subjects'));
    }

    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'education_level' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,published'],
            'files.*' => ['nullable', 'file', 'mimes:pdf,ppt,pptx,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $material->update($data);
        $this->storeFiles($request, $material);

        return redirect()->route('teacher.materials.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        foreach ($material->files as $file) {
            Storage::disk('local')->delete($file->storage_path);
            $file->delete();
        }
        $material->delete();
        return redirect()->route('teacher.materials.index')->with('success', 'Materi berhasil dihapus.');
    }

    public function download(MaterialFile $file)
    {
        abort_unless(\Illuminate\Support\Facades\Storage::disk('local')->exists($file->storage_path), 404);
        return \Illuminate\Support\Facades\Storage::disk('local')->download($file->storage_path, $file->original_name);
    }

    private function storeFiles(Request $request, Material $material): void
    {
        foreach ($request->file('files', []) as $file) {
            if (!$file) continue;
            $path = $file->store('materials');
            MaterialFile::create([
                'material_id' => $material->id,
                'original_name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
                'uploaded_by' => $request->user()->id,
            ]);
        }
    }
}
