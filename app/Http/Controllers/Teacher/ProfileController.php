<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengajar.
     */
    public function edit(Request $request)
    {
        return view('teacher.profile.edit', [
            'user' => $request->user()
        ]);
    }

    /**
     * Memperbarui profil pengajar.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // Validasi hanya data yang boleh diperbarui.
        // Email tidak disertakan karena tidak boleh diubah.
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

        // Upload foto profil baru jika tersedia.
        if ($request->hasFile('photo')) {

            $newPhotoPath = $request->file('photo')
                ->store('profile-photos', 'public');

            if ($newPhotoPath) {

                // Hapus foto lama setelah foto baru berhasil disimpan.
                if ($user->photo_path) {
                    Storage::disk('public')->delete($user->photo_path);
                }

                $data['photo_path'] = $newPhotoPath;
            }
        }

        // Hapus field photo karena kolom database bernama photo_path.
        unset($data['photo']);

        // Simpan perubahan tanpa mengubah email.
        $user->update($data);

        return back()->with(
            'success',
            'Profil berhasil diperbarui.'
        );
    }
}
