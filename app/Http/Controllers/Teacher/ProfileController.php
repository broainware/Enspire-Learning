<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('teacher.profile.edit', [
            'user' => $request->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
        ]);

        $oldPhotoPath = $user->photo_path;
        $newPhotoPath = null;

        // Upload foto baru.
        if ($request->hasFile('photo')) {
            $newPhotoPath = $request->file('photo')
                ->store('profile-photos', 'public');

            if (!$newPhotoPath) {
                return back()
                    ->withErrors(['photo' => 'Foto gagal diunggah.'])
                    ->withInput();
            }
        }

        // Simpan data profil.
        $user->name = $data['name'];
        $user->phone = $data['phone'] ?? null;

        if ($newPhotoPath) {
            $user->photo_path = $newPhotoPath;
        }

        $user->save();

        // Hapus foto lama setelah foto baru berhasil tersimpan.
        if ($newPhotoPath && $oldPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return back()->with(
            'success',
            'Profil berhasil diperbarui.'
        );
    }
}