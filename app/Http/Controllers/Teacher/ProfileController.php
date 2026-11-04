<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;

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

        $photoUrl = null;

        // Upload foto ke Cloudinary.
        if ($request->hasFile('photo')) {
            try {
                $cloudinary = new Cloudinary();

                $result = $cloudinary->uploadApi()->upload(
                    $request->file('photo')->getRealPath(),
                    [
                        'folder' => 'enspire-learning/profile-photos',
                        'public_id' => 'user-' . $user->id,
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
                Log::error('Cloudinary profile upload failed', [
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

        // Simpan URL foto baru.
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
