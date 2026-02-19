<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileManagement;
use Illuminate\Support\Str;
use App\Models\LibraryProjectType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\CollectFile;

class FileManagementController extends Controller
{

    public function index()
    {
        $filesQuery = FileManagement::with(['libraryProject', 'files'])
            ->select(['id', 'file_name', 'file_code', 'project_id', 'created_at', 'file_link'])
            ->latest();

        $files = $filesQuery->get()
            ->groupBy(function ($file) {
                return $file->libraryProject?->title ?? 'Tanpa Kategori';
            });

        $usedProjectIds = $filesQuery->pluck('project_id')->unique()->filter();

        $libraryProjects = LibraryProjectType::where('is_active', 1)
            ->whereIn('id', $usedProjectIds)
            ->orderBy('title')
            ->get()
            ->map(function ($proj) {
                $proj->slug = Str::slug($proj->title);
                return $proj;
            });

        if ($files->has('Tanpa Kategori')) {
            $libraryProjects->push((object)[
                'id' => null,
                'title' => 'Tanpa Kategori',
                'slug' => 'tanpa-kategori',
                'is_active' => 1
            ]);
        }

        return view('library.index', compact('files', 'libraryProjects'));
    }
    public function create()
    {
        $projects = LibraryProjectType::pluck('title', 'id'); // Ambil project untuk dropdown
        return view('library.create', compact('projects'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'file_name' => 'required',
            'file_code' => 'required',
            'project_id' => 'required|exists:library_project_types,id',
        ]);

        // Cek apakah file dengan file_code sudah ada
        $existingFile = FileManagement::where('file_code', $request->input('file_code'))
            ->where('project_id', $request->input('project_id'))
            ->first();

        if ($existingFile) {
            return redirect()->back()->withErrors(['file_code' => 'Dokumen dengan kode file ini sudah ada.']);
        }

        // Simpan data file di FileManagement
        $data = new FileManagement();
        $data->project_id = $request->input('project_id');
        $data->file_name = $request->input('file_name');
        $data->file_code = $request->input('file_code');
        $data->user_id = Auth::id();

        // Proses upload file
        if ($request->hasFile('path_file')) {
            $file = $request->file('path_file');
            $extension = $file->getClientOriginalExtension();
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $count = 0;
            $filename = "{$originalFileName}.{$extension}";
            $newFilename = $filename;

            // Memastikan nama file unik
            while (CollectFile::where('filename', $newFilename)->exists()) {
                $count++;
                $newFilename = "{$originalFileName}_{$count}.{$extension}";
            }

            // Simpan file dan catat path
            $path = $file->storeAs('public/uploads', $newFilename);
            $data->path_file = str_replace('public/', '', $path); // Simpan path file yang sudah diupload
        }

        if ($request->input('file_link')) {
            $data->file_link = $request->input('file_link');
        }

        // Simpan data ke database
        $data->save();

        if ($request->hasFile('path_file')) {
            // Simpan informasi file di CollectFile
            $collectFile = new CollectFile();
            $collectFile->filename = $newFilename;
            $collectFile->link = str_replace('public/', '', $path);;
            $collectFile->collectable_id = $data->id;
            $collectFile->collectable_type = FileManagement::class;
            $collectFile->save();
        }



        return redirect()->route('library.index')->with('success', 'File berhasil diunggah!');
    }


    public function edit($id)
    {
        $file = FileManagement::findOrFail($id);
        $projects = LibraryProjectType::pluck('title', 'id');
        return view('library.edit', compact('file', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $fileManagement = FileManagement::findOrFail($id);

        $request->validate([
            'file_name' => 'required',
            'file_code' => 'required',
            'file_link' => 'required',
            'project_id' => 'required|exists:library_project_types,id',
            'path_file' => 'nullable|file|max:10240', // opsional, max 10MB
        ]);

        // Cek apakah file_code sudah ada (kecuali untuk data ini sendiri)
        $existing = FileManagement::where('file_code', $request->file_code)
            ->where('project_id', $request->project_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return redirect()->back()->withErrors(['file_code' => 'Kode file sudah digunakan di proyek ini.']);
        }

        $fileManagement->file_name = $request->file_name;
        $fileManagement->file_code = $request->file_code;
        $fileManagement->project_id = $request->project_id;
        $fileManagement->file_link = $request->file_link;

        $newFilename = null;
        $path = null;

        if ($request->hasFile('path_file')) {
            $file = $request->file('path_file');
            $extension = $file->getClientOriginalExtension();
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $count = 0;
            $filename = "{$originalFileName}.{$extension}";
            $newFilename = $filename;

            // Pastikan nama unik (sama seperti di store)
            while (\App\Models\CollectFile::where('filename', $newFilename)->exists()) {
                $count++;
                $newFilename = "{$originalFileName}_{$count}.{$extension}";
            }

            // Simpan file
            $path = $file->storeAs('public/uploads', $newFilename);
            $fileManagement->path_file = str_replace('public/', '', $path);

            // Hapus file lama jika ada
            if ($fileManagement->getOriginal('path_file')) {
                Storage::delete('public/' . $fileManagement->getOriginal('path_file'));
            }
        }

        $fileManagement->save();

        // Update atau buat CollectFile jika ada file baru
        if ($request->hasFile('path_file')) {
            // Hapus CollectFile lama
            $fileManagement->files()->delete();

            // Buat yang baru
            $collectFile = new \App\Models\CollectFile();
            $collectFile->filename = $newFilename;
            $collectFile->link = str_replace('public/', '', $path);
            $collectFile->collectable_id = $fileManagement->id;
            $collectFile->collectable_type = FileManagement::class;
            $collectFile->save();
        }

        return redirect()->route('library.index')->with('success', 'File berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Temukan file yang akan dihapus dari FileManagement
        $file = FileManagement::findOrFail($id);

        // Hapus file dari CollectFile terlebih dahulu
        $collectFile = CollectFile::where('collectable_id', $file->id)
            ->where('collectable_type', FileManagement::class)
            ->first();

        if ($collectFile) {
            // Hapus file fisik jika ada di server
            if ($collectFile->link && Storage::disk('public')->exists($collectFile->link)) {
                Storage::disk('public')->delete($collectFile->link);
            }

            // Hapus record dari CollectFile
            $collectFile->delete();
        }

        // Hapus file dari FileManagement
        if ($file->path_file && Storage::disk('public')->exists($file->path_file)) {
            Storage::disk('public')->delete($file->path_file);
        }

        // Hapus record dari FileManagement
        $file->delete();

        return redirect()->route('library.index')->with('success', 'File berhasil dihapus!');
    }

    public function search(Request $request)
    {
        // Validasi input pencarian
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        // Ambil query dari input form
        $query = $request->input('query');

        // Lakukan pencarian berdasarkan file_name atau file_code di FileManagement
        $results = FileManagement::where('file_name', 'LIKE', '%' . $query . '%')
            ->orWhere('file_code', 'LIKE', '%' . $query . '%')
            ->get();

        // Inisialisasi string untuk menampung hasil dalam bentuk teks
        $textResult = "";

        // Jika ada hasil pencarian, tambahkan header
        if ($results->count() > 0) {
            $latestUpdate = $results->max('created_at')->format('d/m/Y');
            $textResult .= "🔍 *Hasil Pencarian untuk:* _" . $query . "_\n\n";
            $textResult .= "📅 *Update terakhir:* _" . $latestUpdate . "_\n\n";
        }

        // Looping melalui hasil pencarian
        foreach ($results as $result) {
            // Cari collectable_id di CollectFile berdasarkan file yang ditemukan
            $collectFile = CollectFile::where('collectable_id', $result->id)
                ->where('collectable_type', FileManagement::class)
                ->first();

            // Tampilkan informasi hasil pencarian
            $textResult .= "📄 *Nama Dokumen*: " . $result->file_name . "\n";
            $textResult .= "📋 *Nomor Dokumen*: " . $result->file_code . "\n";
            $textResult .= "📅 *Tanggal Dibuat*: " . $result->created_at->format('d/m/Y') . "\n";

            // Jika collectFile ditemukan, tambahkan instruksi unduh
            if ($collectFile) {
                $textResult .= "📂 *Panggil Dokumen*: Unduh dengan instruksi: Downloadfile_" . $collectFile->id . "\n";
            } else {
                $textResult .= "⚠️ *File tidak ditemukan di koleksi file*.\n";
            }

            $textResult .= "----------------------------------\n\n"; // Garis pemisah antar hasil
        }

        // Jika tidak ada hasil, kembalikan pesan "Tidak ada hasil"
        if (empty($textResult)) {
            $textResult = "⚠️ Tidak ada file yang ditemukan untuk pencarian: *" . $query . "*";
        }

        // Kembalikan hasil pencarian dalam bentuk teks
        return response($textResult)->header('Content-Type', 'text/plain');
    }
}
