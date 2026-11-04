<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;

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

        // Validasi data profil.
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

        $photoUrl = null;

        // Upload foto ke Cloudinary.
        if ($request->hasFile('photo')) {
            try {
                $cloudinary = new Cloudinary();

                $result = $cloudinary->uploadApi()->upload(
                    $request->file('photo')->getRealPath(),
                    [
                        'folder' => 'enspire-learning/profile-photos/students',
                        'public_id' => 'student-' . $user->id,
                        'overwrite' => true,
                        'resource_type' => 'image',
                    ]
                );

                $photoUrl = $result['secure_url'] ?? null;

                if (!$photoUrl) {
                    throw new \RuntimeException(
                        'Cloudinary tidak mengembalikan URL foto.'
                    );
                }

            } catch (\Throwable $e) {
                Log::error('Cloudinary student profile upload failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);

                return back()
                    ->withErrors([
                        'photo' => 'Foto gagal diunggah. Periksa konfigurasi Cloudinary.'
                    ])
                    ->withInput();
            }
        }

        // Simpan informasi profil.
        $user->name = $data['name'];
        $user->phone = $data['phone'] ?? null;

        // Simpan URL Cloudinary jika ada foto baru.
        if ($photoUrl) {
            $user->photo_path = $photoUrl;
        }

        $user->save();

        return back()->with(
            'success',
            'Profil berhasil diperbarui.'
        );
    }
}
