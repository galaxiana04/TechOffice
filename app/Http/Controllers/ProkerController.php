<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proker;
use App\Models\ProkerMonthly;
use App\Models\Unit;
use App\Models\CollectFile;
use App\Services\TelegramService;

use Carbon\Carbon; // Add this at the top of your controller if not already present
use Illuminate\Support\Facades\Storage;

class ProkerController extends Controller
{

    public function show($unitid)
    {
        $units = Unit::where('name', 'not like', '%Senior Manager%')->get();
        $user = auth()->user();
        $userId = $user->id;


        // Get the unit ID based on the cleaned unit name, or null if not found
        $unitRecord = Unit::where('id', $unitid)->first();
        $unitId = $unitRecord ? $unitRecord->id : null;

        // Get the current month and year in 'Y-m' format (e.g., "2025-03")
        $monthYear = Carbon::today()->format('Y-m');

        // Fetch prokers for the user's unit and current month
        if ($unitId) {
            if ($userId == 37 || $userId == 1) {
                $prokers = Proker::where('unit_id', $unitId)
                    ->with(['prokerMonthly' => function ($query) {
                        $query->latest('id');
                    }])
                    ->where(function ($query) use ($monthYear) {
                        // Ambil semua proker bulan ini
                        $query->whereHas('prokerMonthly', function ($q) use ($monthYear) {
                            $q->where('date', $monthYear);
                        })
                            // Atau proker dari bulan sebelumnya yang belum 100%
                            ->orWhereHas('prokerMonthly', function ($q) use ($monthYear) {
                                $q->where('date', '<', $monthYear)
                                    ->where('percentage', '<', 100);
                            })
                            // Atau proker yang belum ada progress sama sekali
                            ->orWhereDoesntHave('prokerMonthly');
                    })
                    ->get();
            } else {
                $prokers = Proker::where('unit_id', $unitId)
                    ->with(['prokerMonthly' => function ($query) {
                        $query->latest('id');
                    }])->where('ishide', false)->where('proker_created_at', '<=', $monthYear) // Pastikan proker_created_at <= bulan ini
                    ->where(function ($query) use ($monthYear) {
                        // Ambil semua proker bulan ini
                        $query->whereHas('prokerMonthly', function ($q) use ($monthYear) {
                            $q->where('date', $monthYear);
                        })
                            // Atau proker dari bulan sebelumnya yang belum 100%
                            ->orWhereHas('prokerMonthly', function ($q) use ($monthYear) {
                                $q->where('date', '<', $monthYear)
                                    ->where('percentage', '<', 100);
                            })
                            // Atau proker yang belum ada progress sama sekali
                            ->orWhereDoesntHave('prokerMonthly');
                    })
                    ->get();
            }
        } else {
            $prokers = collect();
        }

        return view('proker.show', compact('units',  'prokers', 'monthYear', 'unitId', 'userId', 'unitRecord'));
    }

    public function index()
    {
        $currentMonthYear = Carbon::today()->format('Y-m');

        $units = Unit::with(['prokers' => function ($query) use ($currentMonthYear) {
            $query->where('proker_created_at', '<=', $currentMonthYear)
                ->where('ishide', false) // Hanya ambil proker yang tidak disembunyikan
                ->with(['prokerMonthly' => function ($q) use ($currentMonthYear) {
                    $q->where('date', $currentMonthYear);
                }]);
        }])->where('name', 'not like', '%Manager%')->where('is_technology_division', true)->get();

        // Hitung statistik per unit
        $units = $units->map(function ($unit) use ($currentMonthYear) {
            $totalProkers = $unit->prokers->count();
            $completedProkers = $unit->prokers->filter(function ($proker) use ($currentMonthYear) {
                $latestMonthly = $proker->prokerMonthly->first();
                return $latestMonthly && $latestMonthly->percentage == 100;
            })->count();
            $incompleteProkers = $totalProkers - $completedProkers;

            $unit->stats = [
                'total_prokers' => $totalProkers,
                'completed_prokers' => $completedProkers,
                'incomplete_prokers' => $incompleteProkers,
                'completed_percentage' => $totalProkers > 0 ? round(($completedProkers / $totalProkers) * 100, 2) : 0,
                'incomplete_percentage' => $totalProkers > 0 ? round(($incompleteProkers / $totalProkers) * 100, 2) : 0,
            ];

            return $unit;
        });

        // Urutkan unit berdasarkan apakah nama mengandung "Manager" di belakang
        $units = $units->sortBy(function ($unit) {
            return str_ends_with($unit->name, 'Manager') ? 0 : 1;
        });

        return view('proker.index', compact('units', 'currentMonthYear'));
    }


    public function broadcast()
    {
        $units = Unit::where('name', 'not like', '%Manager%')->get();

        foreach ($units as $unitRecord) {
            $unitId = $unitRecord->id ?? null;

            $prokers = collect(); // Default collection kosong

            if ($unitId) {
                $prokers = Proker::where('unit_id', $unitId)
                    ->with('prokerMonthly')
                    ->where(function ($query) {
                        $query->whereHas('prokerMonthly', function ($q) {
                            $q->whereIn('id', function ($subQuery) {
                                $subQuery->selectRaw('MAX(id)')
                                    ->from('proker_monthly')
                                    ->whereColumn('proker_id', 'prokers.id');
                            })->where('percentage', '<', 100);
                        })->orWhereDoesntHave('prokerMonthly');
                    })
                    ->get();
            }

            Carbon::setLocale('id');
            $bulanTahun = Carbon::today()->isoFormat('MMMM Y'); // e.g., April 2025
            $deadline = Carbon::now()->endOfMonth()->subDays(2)->format('d-m-Y'); // e.g., 28-04-2025

            $broadcast = "📢 Target Proker danBulan - {$bulanTahun} dengan deadline *{$deadline}*\n";
            $broadcast .= "🏢 Unit: *" . ($unitRecord->name ?? 'Tidak diketahui') . "*\n\n";

            if ($prokers->count()) {
                $broadcast .= "Daftar Proker yang belum mencapai 100% progress (total: {$prokers->count()}) dapat dicek pada link berikut:\n";
                $broadcast .= "📍 Cek dan update Proker di: https://intip.in/techofficeproker\n\n";
                $broadcast .= "📈 Progres yang sudah diinput akan direview GM Teknologi menjadi penilaian Divisi Teknologi ✨. Terimakasih 💪";
            } else {
                $broadcast .= "✅ Seluruh *Proker* telah selesai 100% atau belum ada data Proker bulan ini. 🎉👏\n";
                $broadcast .= "📍 Tetap semangat dan pertahankan kinerja baik Anda! 💼💡";
            }

            // Kirim broadcast ke unit tertentu (atau bisa diubah jadi semua unit)
            // if ($unitRecord->name == "Product Engineering") {

            // }

            TelegramService::ujisendunit($unitRecord->name, $broadcast);
        }

        return response()->json(['message' => 'Success'], 200);
    }

    public function prokerBroadcast($id)
    {
        // Find the proker by ID with its associated unit
        $proker = Proker::with('unit')->find($id);

        if (!$proker || !$proker->unit) {
            return response()->json(['message' => 'Proker or associated unit not found'], 404);
        }

        $unit = $proker->unit;
        $unitId = $unit->id;

        // Get the latest proker monthly record for this proker
        $latestProkerMonthly = $proker->prokerMonthly()
            ->latest('id')
            ->first();

        Carbon::setLocale('id');
        $bulanTahun = Carbon::today()->isoFormat('MMMM Y'); // e.g., April 2025
        $deadline = Carbon::now()->endOfMonth()->subDays(2)->format('d-m-Y'); // e.g., 28-04-2025

        // Construct the broadcast message
        $broadcast = "📢 Target Proker - {$bulanTahun} dengan deadline *{$deadline}*\n";
        $broadcast .= "🏢 Unit: *" . ($unit->name ?? 'Tidak diketahui') . "*\n";
        $broadcast .= "📋 Proker: *" . ($proker->name ?? 'Tidak diketahui') . "*\n\n";


        if ($latestProkerMonthly && $latestProkerMonthly->percentage < 100) {
            $broadcast .= "📊 Progres saat ini: {$latestProkerMonthly->percentage}%\n";
            $broadcast .= "📍 Cek dan update Proker di: https://intip.in/techofficeproker\n\n";
            $broadcast .= "📈 Progres yang sudah diinput akan direview GM Teknologi menjadi penilaian Divisi Teknologi ✨. Terimakasih 💪";
        } elseif ($latestProkerMonthly && $latestProkerMonthly->percentage >= 100) {
            $broadcast .= "✅ Proker telah selesai 100%! 🎉👏\n";
            $broadcast .= "📍 Tetap semangat dan pertahankan kinerja baik Anda! 💼💡";
        } else {
            $broadcast .= "⚠️ Belum ada progres untuk Proker ini.\n";
            $broadcast .= "📍 Segera update progres di: https://intip.in/techofficeproker\n\n";
            $broadcast .= "📈 Progres yang sudah diinput akan direview GM Teknologi menjadi penilaian Divisi Teknologi ✨. Terimakasih 💪";
        }

        // Send the broadcast to the unit's WhatsApp group
        TelegramService::ujisendunit($unit->name, $broadcast);

        return response()->json(['message' => 'Broadcast sent successfully'], 200);
    }








    public function getProker($id)
    {
        $monthYear = now()->format('Y-m'); // Bulan saat ini, misalnya '2025-05'
        $previousMonthYear = now()->subMonth()->format('Y-m'); // Bulan lalu, misalnya '2025-04'

        $proker = Proker::with([
            'prokerMonthly' => function ($query) use ($monthYear, $previousMonthYear) {
                $query->whereIn('date', [$monthYear, $previousMonthYear])
                    ->orderByDesc('id')
                    ->with('files');
            }
        ])->find($id);

        if (!$proker) {
            return response()->json(['error' => 'Proker not found'], 404);
        }

        // Transform response to include current and previous month data
        $proker->current_monthly = $proker->prokerMonthly->where('date', $monthYear)->first();
        $proker->previous_monthly = $proker->prokerMonthly->where('date', $previousMonthYear)->first();
        unset($proker->prokerMonthly); // Remove original prokerMonthly to avoid redundancy



        return response()->json($proker);
    }

    public function searchProker(Request $request)
    {
        $monthYear = $request->input('month_year'); // e.g., '2025-05'
        $unitId = $request->input('unit_id');

        // Validate inputs
        if (!$monthYear || !$unitId) {
            return response()->json(['error' => 'month_year and unit_id are required'], 400);
        }

        // Calculate previous month
        $previousMonthYear = \Carbon\Carbon::parse($monthYear)->subMonth()->format('Y-m');

        // Query Proker records
        $prokers = Proker::where('unit_id', $unitId)
            ->where('proker_created_at', '<=', $monthYear) // Compare as string YYYY-MM
            ->with([
                'prokerMonthly' => function ($query) use ($monthYear, $previousMonthYear) {
                    $query->whereIn('date', [$monthYear, $previousMonthYear])
                        ->orderByDesc('id')
                        ->with('files');
                }
            ])
            ->where(function ($query) use ($monthYear, $previousMonthYear) {
                // Include Proker records that:
                // 1. Have a prokerMonthly for the specified month_year
                // 2. Have a prokerMonthly from a previous month with percentage < 100
                // 3. Have no prokerMonthly records
                $query->whereHas('prokerMonthly', function ($q) use ($monthYear) {
                    $q->where('date', $monthYear);
                })
                    ->orWhereHas('prokerMonthly', function ($q) use ($monthYear) {
                        $q->where('date', '<', $monthYear)
                            ->where('percentage', '<', 100);
                    })
                    ->orWhereDoesntHave('prokerMonthly');
            })
            ->get();

        // Transform the response to include current and previous month data explicitly
        $prokers->each(function ($proker) use ($monthYear, $previousMonthYear) {
            $proker->current_monthly = $proker->prokerMonthly->where('date', $monthYear)->first();
            $proker->previous_monthly = $proker->prokerMonthly->where('date', $previousMonthYear)->first();
            unset($proker->prokerMonthly); // Remove the original prokerMonthly to avoid redundancy
        });

        return response()->json($prokers);
    }




    public function historyProker(Request $request)
    {
        $prokerId = $request->input('proker_id');

        $prokers = Proker::where('id', $prokerId)
            ->with(['prokerMonthly' => function ($query) {
                $query->orderByDesc('date') // atau 'id' kalau prefer urut berdasarkan id
                    ->with('files');
            }])
            ->get();

        return response()->json($prokers);
    }



    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|string|max:255',
            'proker_created_at' => 'required|date_format:Y-m',
            'ispercentageflexible' => 'sometimes|boolean',
        ]);

        $proker = Proker::create([
            'unit_id' => $request->unit_id,
            'name' => $request->name,
            'proker_created_at' => $request->proker_created_at,
            'ispercentageflexible' => $request->boolean('ispercentageflexible', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program Kerja berhasil ditambahkan!',
            'proker' => $proker
        ]);
    }
    public function storeMonthly(Request $request)
    {
        $request->validate([
            'proker_id' => 'required|exists:prokers,id',
            'date' => 'required|date_format:Y-m',
            'percentage' => 'required|numeric|between:0,100',
            'files.*' => 'nullable'
        ]);
        $userName = auth()->user()->name ?? "Unknown";

        $prokerMonthly = ProkerMonthly::create([
            'proker_id' => $request->proker_id,
            'date' => $request->date,
            'percentage' => $request->percentage
        ]);

        // Handle multiple file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $uploadedFile) {
                $filename = $uploadedFile->getClientOriginalName();
                $fileFormat = $uploadedFile->getClientOriginalExtension();
                $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;
                $filename = $filenameWithUserAndFormat;

                $count = 0;
                $newFilename = $filename;
                // Check if the file with the same name exists and rename it
                while (CollectFile::where('filename', $newFilename)->exists()) {
                    $count++;
                    $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . $fileFormat;
                }

                $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                // Save file record to the database
                $File = new CollectFile();
                $File->filename = $newFilename;
                $File->link = str_replace('public/', '', $path);
                $File->collectable_id = $prokerMonthly->id;
                $File->collectable_type = ProkerMonthly::class;
                $File->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'ProkerMonthly berhasil ditambahkan!',
            'proker_monthly' => $prokerMonthly
        ]);
    }

    public function destroy($id)
    {
        $proker = Proker::find($id);

        if (!$proker) {
            return response()->json(['message' => 'Proker not found'], 404);
        }

        // Delete associated ProkerMonthly records and their files
        $prokerMonthlies = ProkerMonthly::where('proker_id', $id)->get();
        foreach ($prokerMonthlies as $prokerMonthly) {
            // Delete associated files
            $files = CollectFile::where('collectable_id', $prokerMonthly->id)
                ->where('collectable_type', ProkerMonthly::class)
                ->get();
            foreach ($files as $file) {
                Storage::delete('public/uploads/' . $file->filename);
                $file->delete();
            }
            $prokerMonthly->delete();
        }

        // Delete the proker
        $proker->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program Kerja berhasil dihapus!'
        ]);
    }
    public function toggleHide($id)
    {
        if (!is_numeric($id)) {
            return response()->json([
                'success' => false,
                'message' => 'ID tidak valid.'
            ], 400);
        }

        $proker = Proker::find($id);

        if (!$proker) {
            return response()->json([
                'success' => false,
                'message' => 'Program Kerja tidak ditemukan.'
            ], 404);
        }

        $proker->ishide = !$proker->ishide;
        $proker->save();

        return response()->json([
            'success' => true,
            'message' => $proker->ishide
                ? 'Program Kerja berhasil disembunyikan!'
                : 'Program Kerja berhasil ditampilkan kembali!'
        ]);
    }
}
