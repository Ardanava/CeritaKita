<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditorUploadController extends Controller
{
    // ===================== START: Upload Image From Editor =====================
    public function image(Request $request)
    {
        // ---------- START: Validasi file ----------
        $request->validate([
            'image' => ['required','image','mimes:jpg,jpeg,png,webp,gif','max:12288'], // max ~12MB
        ]);
        // ---------- END: Validasi file ----------


        // ---------- START: Ambil file & siapkan folder ----------
        $file   = $request->file('image');
        $folder = 'editor-images/'.date('Y/m'); // e.g. editor-images/2025/11
        // ---------- END: Ambil file & siapkan folder ----------


        // ---------- START: Tentukan nama & ekstensi file ----------
        $ext = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
        $filename = Str::uuid().'.'.$ext;
        // ---------- END: Tentukan nama file ----------


        // ---------- START: Simpan file ke disk public ----------
        // hasil: storage/app/public/editor-images/2025/11/uuid.jpg
        $path = $file->storeAs($folder, $filename, 'public');
        // ---------- END: Simpan file ke disk public ----------


        // ---------- START: Build public URL ----------
        // Storage::url() → “/storage/editor-images/...”
        // asset() → pastikan absolute tanpa double slash
        $url = asset('storage/'.$path);
        // ---------- END: Build public URL ----------


        // ---------- START: Response JSON untuk editor ----------
        return response()->json(['url' => $url], 201);
        // ---------- END: Response JSON ----------
    }
    // ===================== END: Upload Image From Editor =====================
}
