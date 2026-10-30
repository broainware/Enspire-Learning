<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil siswa.
     */
    public function edit(Request $request)
    {
        return view('student.profile.edit', [
            'user' => $request->user()
        ]);
    }

    /**
     * Memperbarui profil siswa.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // Validasi hanya data yang boleh diedit.
        // Email tidak dapat diubah melalui halaman profil.
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

        // Proses upload foto profil.
        if ($request->hasFile('photo')) {

            $newPhotoPath = $request->file('photo')
                ->store('profile-photos', 'public');

            // Foto lama dihapus setelah foto baru berhasil disimpan.
            if ($newPhotoPath) {

                if ($user->photo_path) {
                    Storage::disk('public')->delete($user->photo_path);
                }

                $data['photo_path'] = $newPhotoPath;
            }
        }

        // Hapus field photo karena nama kolom database adalah photo_path.
        unset($data['photo']);

        // Simpan perubahan profil tanpa mengubah email.
        $user->update($data);

        return back()->with(
            'success',
            'Profil berhasil diperbarui.'
        );
    }
}
