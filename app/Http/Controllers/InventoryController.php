<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryLoan;
use App\Models\InventoryKind;
use App\Models\CollectFile;
use \App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function index()
    {
        return view('inventory.index', [
            'inventories' => Inventory::with(['kind', 'files'])->get(),
            'kinds' => InventoryKind::all(),
            'users' => User::all(),
        ]);
    }

    public function getInventories()
    {
        try {
            $inventories = Inventory::with('kind')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'files' => $item->files->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'filename' => $file->filename,
                            'link' => asset('storage/uploads/' . rawurlencode(str_replace('uploads/', '', $file->link))),
                        ];
                    }),
                    'name' => $item->name,
                    'kind_name' => $item->kind->name,
                    'quantity_total' => $item->quantity_total,
                    'quantity_available' => $item->quantity_available,
                    'assetcode' => $item->assetcode, // Tambahkan assetcode
                    'machinecode' => $item->machinecode, // Tambahkan machinecode
                ];
            });
            return response()->json(['success' => true, 'inventories' => $inventories]);
        } catch (\Exception $e) {
            Log::error('Error fetching inventories: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server'], 500);
        }
    }

    public function storeKind(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            InventoryKind::create($data);

            return response()->json(['success' => true, 'message' => 'Jenis barang berhasil ditambahkan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error storing inventory kind: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server'], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'inventory_kind_id' => 'required|exists:inventory_kinds,id',
                'quantity_total' => 'required|integer|min:1',
                'files' => 'nullable|array',
            ]);

            $data['quantity_available'] = $data['quantity_total'];
            $inventory = Inventory::create($data);
            $userName = Auth::user()->name;

            $user = Auth::user();
            foreach ($request->file('files') as $key => $uploadedFile) {
                // Dapatkan nama file yang diunggah
                $filename = $uploadedFile->getClientOriginalName();

                // Dapatkan ekstensi file
                $fileFormat = $uploadedFile->getClientOriginalExtension();

                // Hapus ekstensi file dari nama file
                $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

                // Gabungkan nama file (tanpa ekstensi), nama pengguna, dan format file
                $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;

                // Sekarang, $filenameWithUserAndFormat berisi nama file yang dihasilkan dengan nama pengguna dan format file
                $filename = $filenameWithUserAndFormat;

                // Periksa apakah nama file sudah ada
                $count = 0;
                $newFilename = $filename;
                while (CollectFile::where('filename', $newFilename)->exists()) {
                    $count++;
                    $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                }

                // Simpan file di storage/app/public/uploads
                $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                // Simpan file terkait di database
                $newmemoFile = new CollectFile();
                $newmemoFile->filename = $newFilename;
                $newmemoFile->link = str_replace('public/', '', $path);
                $newmemoFile->collectable_id = $inventory->id; // Menghubungkan file dengan inventory
                $newmemoFile->collectable_type = Inventory::class; // Tipe polimorfik
                $newmemoFile->save();
            }

            return response()->json(['success' => true, 'message' => 'Barang berhasil ditambahkan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error storing inventory: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server'], 500);
        }
    }

    public function borrow(Request $request, Inventory $inventory)
    {
        try {
            $request->validate(
                [
                    'quantity' => 'required|integer|min:1',
                    'user_id' => 'required|exists:users,id',

                ]
            );

            if ($inventory->quantity_available < $request->quantity) {
                return response()->json(['success' => false, 'message' => 'Stok tidak cukup'], 400);
            }

            InventoryLoan::create([
                'inventory_id' => $inventory->id,
                'user_id' => $request->user_id,
                'quantity' => $request->quantity,
                'borrowed_at' => now(),
                'status' => 'dipinjam',
            ]);

            $inventory->decrement('quantity_available', $request->quantity);

            return response()->json(['success' => true, 'message' => 'Peminjaman berhasil']);
        } catch (\Exception $e) {
            Log::error('Error borrowing inventory: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server'], 500);
        }
    }

    public function showLoans(Inventory $inventory)
    {
        try {
            $loans = InventoryLoan::where('inventory_id', $inventory->id)
                ->with('user')
                ->get()
                ->map(function ($loan) {
                    return [
                        'id' => $loan->id,
                        'user_id' => $loan->user_id,
                        'user_name' => $loan->user->name,
                        'quantity' => $loan->quantity,
                        'status' => $loan->status,
                        'borrowed_at' => $loan->borrowed_at,
                        'returned_at' => $loan->returned_at
                    ];
                });

            return response()->json(['success' => true, 'loans' => $loans]);
        } catch (\Exception $e) {
            Log::error('Error showing loans: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server'], 500);
        }
    }

    public function returnLoan(Request $request, InventoryLoan $loan)
    {
        try {
            if ($loan->status === 'dikembalikan') {
                return response()->json(['success' => false, 'message' => 'Barang sudah dikembalikan'], 400);
            }

            $loan->update([
                'status' => 'dikembalikan',
                'returned_at' => now(),
            ]);

            $loan->inventory->increment('quantity_available', $loan->quantity);

            return response()->json(['success' => true, 'message' => 'Barang berhasil dikembalikan']);
        } catch (\Exception $e) {
            Log::error('Error returning loan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server'], 500);
        }
    }
    public function updateAssetcode(Request $request, Inventory $inventory)
    {
        try {
            $data = $request->validate([
                'assetcode' => 'required|string|max:255',
            ]);

            $inventory->update([
                'assetcode' => $data['assetcode'],
            ]);

            return response()->json(['success' => true, 'message' => 'Kode aset berhasil diperbarui']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating assetcode: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server'], 500);
        }
    }

    public function updateMachinecode(Request $request, Inventory $inventory)
    {
        try {
            $data = $request->validate([
                'machinecode' => 'required|string|max:255',
            ]);

            $inventory->update([
                'machinecode' => $data['machinecode'],
            ]);

            return response()->json(['success' => true, 'message' => 'Kode mesin berhasil diperbarui']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating machinecode: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server'], 500);
        }
    }
}
