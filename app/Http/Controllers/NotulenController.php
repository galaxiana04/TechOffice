<?php

namespace App\Http\Controllers;

use App\Imports\RawprogressreportsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Notulen;
use App\Models\TopicNotulen;
use App\Models\CollectFile;
use App\Models\AgendaNotulen;
use App\Models\IssueNotulen;
use App\Models\ProjectType;
use App\Models\SolutionIssue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon; // Import Carbon class
use Exception;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser;

class NotulenController extends Controller
{
    public function index()
    {
        $projectTypes = ProjectType::where('id', 2)->get();
        $agendas = AgendaNotulen::all();
        $notulens = Notulen::with([
            'files',
            'agendaNotulen',
            'topicnotulens',
            'topicnotulens.issueNotulens.solutions'
        ])->get();

        return view('notulen.index', compact('projectTypes', 'agendas', 'notulens'));
    }

    public function getNotulensByProjectType($projectTypeId)
    {
        $notulens = Notulen::with([
            'files',
            'agendaNotulen',
            'topicnotulens.issueNotulens.solutions'
        ])
            ->whereHas('agendaNotulen', function ($query) use ($projectTypeId) {
                $query->where('project_type_id', $projectTypeId);
            })
            ->get();

        // Format deadline date
        foreach ($notulens as $notulen) {
            foreach ($notulen->topicnotulens as $topic) {
                foreach ($topic->issueNotulens as $issue) {
                    foreach ($issue->solutions as $solution) {
                        $solution->formatted_deadlinedate = optional(Carbon::parse($solution->deadlinedate))->format('d/m/Y');
                    }
                }
            }
        }

        return response()->json($notulens);
    }


    public function storetopicnotulen(Request $request)
    {
        // Validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'notulen_id' => 'required|exists:notulens,id',
            'files.*' => 'file' // Maksimum 2MB per file
        ]);

        try {
            // Simpan data ke database
            $topicNotulen = TopicNotulen::create([
                'title' => $request->title,
                'notulen_id' => $request->notulen_id,
            ]);





            return response()->json(['success' => 'Topic Notulen berhasil ditambahkan!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }



    public function extractFromPdf(Request $request)
    {
        $apiKey = "AIzaSyBiPYVsVqi5R6HIU-2iSjJxrNIlw-TygQA";
        $geminiModel = "gemini-1.5-flash";

        try {
            // Validasi file PDF
            $request->validate([
                'pdf' => 'required' // Maksimal 2MB
            ]);

            // Baca isi PDF menggunakan PDFParser
            $pdfPath = $request->file('pdf')->getPathname();
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $pdfContent = $pdf->getText(); // Ambil teks dari PDF

            // Prompt untuk ekstraksi
            $prompt = "
            Ekstrak informasi berikut dari teks PDF dalam format JSON:
            {
                \"topic\": \"Judul notulen (diambil dari bagian 'Topik' atau 'Dasar Pembahasan')\",
                \"issue\": \"Masalah utama (biasanya diawali dengan kata 'Menindaklanjuti')\",
                \"place\": \"Tempat pertemuan (diambil dari bagian yang menyebutkan lokasi pertemuan)\",
                \"notulen_time_start\": \"Tanggal dan waktu mulai (gabungan dari hari/tanggal dan waktu mulai pertemuan, dalam format YYYY-MM-DDTHH:MM)\",
                 \"notulen_time_end\": \"Tanggal dan waktu mulai (gabungan dari hari/tanggal dan waktu mulai pertemuan, dalam format YYYY-MM-DDTHH:MM)\",
                \"deadlinedate\": \"Tanggal batas waktu (diambil jika ada, dalam format YYYY-MM-DD)\"
            }
            Berikut teks yang perlu diekstrak:
            ---------------------
            $pdfContent
            ";


            // Kirim ke Gemini API
            $response = Http::timeout(240)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/$geminiModel:generateContent?key=$apiKey", [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                    'response_schema' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'topic' => ['type' => 'STRING'],
                            'issue' => ['type' => 'STRING'],
                            'place' => ['type' => 'STRING'],
                            'notulen_time_start' => ['type' => 'STRING'],
                            'notulen_time_end' => ['type' => 'STRING'],
                            'deadlinedate' => ['type' => 'STRING']
                        ]
                    ]
                ]
            ]);


            // Cek jika request ke API gagal
            if ($response->failed()) {
                throw new \Exception("Gagal mengambil data dari Gemini API: " . $response->body());
            }

            // Ambil hasil ekstraksi
            $result = $response->json();
            $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Konversi respons ke array JSON
            $extractedData = json_decode($textResponse, true);

            // Pastikan output tetap dalam format JSON
            return response()->json([
                "success" => true,
                "data" => $extractedData
            ]);
        } catch (\Exception $e) {
            // Catat error ke dalam log aplikasi
            Log::error("Error saat ekstraksi PDF: " . $e->getMessage());

            return response()->json([
                "success" => false,
                "message" => "Terjadi kesalahan: " . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required',

            'notulen_time_start' => 'required',
            'notulen_time_end' => 'required',
            'place' => 'required|string|max:255',

            'agenda_notulen_id' => 'required|exists:agenda_notulens,id',
        ]);

        sleep(2); // Simulasi delay agar loading terlihat

        $userName = auth()->user()->name ?? 'unknown';
        $notulen = Notulen::create([
            'number' => $request->number,

            'notulen_time_start' => $request->notulen_time_start,
            'notulen_time_end' => $request->notulen_time_end,
            'place' => $request->place,
            'user_id' => auth()->id(),
            'agenda_notulen_id' => $request->agenda_notulen_id,

        ]);
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $uploadedFile) {
                $filename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $fileFormat = $uploadedFile->getClientOriginalExtension();
                $filenameWithUser = $filename . '_' . $userName . '.' . $fileFormat;

                // Cek duplikasi file
                $count = 0;
                $newFilename = $filenameWithUser;
                while (CollectFile::where('filename', $newFilename)->exists()) {
                    $count++;
                    $newFilename = $filename . '_' . $count . '.' . $fileFormat;
                }

                $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                CollectFile::create([
                    'filename' => $newFilename,
                    'link' => str_replace('public/', '', $path),
                    'collectable_id' => $notulen->id,
                    'collectable_type' => Notulen::class,
                ]);
            }
        }






        return response()->json(['success' => 'Notulen berhasil ditambahkan!']);
    }




    public function agendastore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_type_id' => 'required|exists:project_types,id',
        ]);


        $agenda = AgendaNotulen::create([
            'name' => $request->name,
            'project_type_id' => $request->project_type_id
        ]);


        return response()->json(['success' => 'Agenda berhasil ditambahkan!']);
    }





    public function update(Request $request, $id)
    {
        $notulen = Notulen::findOrFail($id);
        $notulen->update(['status' => 'close']);

        return response()->json(['success' => 'Status notulen berhasil ditutup!']);
    }

    public function updateissue(Request $request)
    {
        $notulen = Notulen::find($request->id);
        if (!$notulen) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan!'], 404);
        }

        $notulen->issue = $request->issue;
        if ($notulen->save()) {
            return response()->json(['status' => 'success', 'message' => 'Notulen berhasil diperbarui!']);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal memperbarui notulen.']);
    }
    public function storeIssue(Request $request)
    {
        $request->validate([
            'topic_notulen_id' => 'required|exists:topic_notulens,id',
            'issue' => 'required|string'
        ]);

        try {
            $issue = IssueNotulen::create([
                'topic_notulen_id' => $request->topic_notulen_id,
                'issue' => $request->issue
            ]);

            return response()->json(['success' => 'Issue berhasil ditambahkan ke topic!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan issue: ' . $e->getMessage()], 500);
        }
    }
    public function storeSolution(Request $request)
    {
        $request->validate([
            'issue_notulen_id' => 'required|exists:issue_notulens,id',
            'issue' => 'required|string',
            'pic' => 'required|string',
            'status' => 'required|in:open,in_progress,resolved',
            'deadlinedate' => 'nullable|date'
        ]);

        try {
            $solution = SolutionIssue::create([
                'issue_notulen_id' => $request->issue_notulen_id,
                'followup' => $request->issue,
                'pic' => $request->pic,
                'status' => $request->status,
                'deadlinedate' => $request->deadlinedate
            ]);

            return response()->json(['success' => 'Solution berhasil ditambahkan!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan solution: ' . $e->getMessage()], 500);
        }
    }

    private function updateHierarchicalStatus($model, $level)
    {
        if ($level === 'solution') {
            $solution = $model;
            $issue = $solution->issueNotulen;

            // Cek apakah semua solution dalam issue sudah closed
            $allSolutionsClosed = $issue->solutions->every(function ($sol) {
                return $sol->status === 'closed';
            });

            if ($allSolutionsClosed && $issue->status !== 'closed') {
                $issue->status = 'closed';
                $issue->save();
                $this->updateHierarchicalStatus($issue, 'issue');
            } elseif (!$allSolutionsClosed && $issue->status === 'closed') {
                // Jika ada solution yang open dan issue masih closed, ubah ke open
                $issue->status = 'open';
                $issue->save();
                $this->updateHierarchicalStatus($issue, 'issue');
            }
        } elseif ($level === 'issue') {
            $issue = $model;
            $topic = $issue->topicNotulen;

            // Cek apakah semua issue dalam topic sudah closed
            $allIssuesClosed = $topic->issueNotulens->every(function ($iss) {
                return $iss->status === 'closed';
            });

            if ($allIssuesClosed && $topic->status !== 'closed') {
                $topic->status = 'closed';
                $topic->save();
                $this->updateHierarchicalStatus($topic, 'topic');
            } elseif (!$allIssuesClosed && $topic->status === 'closed') {
                // Jika ada issue yang open dan topic masih closed, ubah ke open
                $topic->status = 'open';
                $topic->save();
                $this->updateHierarchicalStatus($topic, 'topic');
            }
        } elseif ($level === 'topic') {
            $topic = $model;
            $notulen = $topic->notulen;

            // Cek apakah semua topic dalam notulen sudah closed
            $allTopicsClosed = $notulen->topicnotulens->every(function ($top) {
                return $top->status === 'closed';
            });

            if ($allTopicsClosed && $notulen->status !== 'closed') {
                $notulen->status = 'closed';
                $notulen->save();
            } elseif (!$allTopicsClosed && $notulen->status === 'closed') {
                // Jika ada topic yang open dan notulen masih closed, ubah ke open
                $notulen->status = 'open';
                $notulen->save();
            }
        }
    }

    // Modifikasi fungsi toggleSolutionStatus untuk memanggil updateHierarchicalStatus
    public function toggleSolutionStatus(Request $request, $id)
    {
        try {
            $solution = SolutionIssue::findOrFail($id);
            $request->validate(['status' => 'required|in:open,closed']);

            $solution->status = $request->status;
            $solution->save();

            // Perbarui status berjenjang setelah status solution diubah
            $this->updateHierarchicalStatus($solution, 'solution');

            return response()->json(['success' => 'Status solution berhasil diubah!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengubah status: ' . $e->getMessage()], 500);
        }
    }


    public function upload()
    {
        return view('notulen.upload');
    }

    public function importExcelsistem(Request $request)
    {
        $jenisupload = $request->jenisupload;

        if ($jenisupload == "formatprogress") {
            $hasil = $this->formatprogress($request);
        }
        return $hasil;
    }

    public function formatprogress(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            $import = new RawprogressreportsImport();
            $revisiData = Excel::toCollection($import, $file)->first();

            if ($revisiData->isEmpty()) {
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }

            $processedData = $this->progressreportexported($revisiData);

            DB::beginTransaction();

            // Buat atau dapatkan AgendaNotulen
            $agenda = AgendaNotulen::firstOrCreate(
                ['name' => $processedData['Agenda']],
                ['project_type_id' => $processedData['project_type_id']]
            );

            // Cek apakah Notulen sudah ada
            $existingNotulen = Notulen::where([
                'number' => $processedData['notulen_number'],
                'agenda_notulen_id' => $agenda->id
            ])->first();

            if ($existingNotulen) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Notulen sudah ada di database. Proses tidak dilanjutkan.'
                ], 409);
            }

            // Jika tidak ada, buat Notulen baru
            $notulen = Notulen::create([
                'number' => $processedData['notulen_number'],
                'notulen_time_start' => Carbon::createFromFormat('d/m/Y', $processedData['start_notulen'])->format('Y-m-d'),
                'notulen_time_end' => Carbon::createFromFormat('d/m/Y', $processedData['end_notulen'])->format('Y-m-d'),
                'place' => $processedData['place'],
                'agenda_notulen_id' => $agenda->id,
                'user_id' => auth()->id() ?? 1,
            ]);

            // Jika Notulen gagal dibuat, hentikan proses
            if (!$notulen) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Gagal membuat Notulen.'
                ], 500);
            }

            // Proses topic
            foreach ($processedData['list_topic'] as $topicData) {
                if (empty($topicData['topic'])) {
                    continue;
                }

                $topic = TopicNotulen::create([
                    'title' => $topicData['topic'],
                    'notulen_id' => $notulen->id,
                ]);

                foreach ($topicData['details'] as $detail) {
                    if (empty($detail['issue'])) {
                        continue;
                    }

                    $issue = IssueNotulen::create([
                        'topic_notulen_id' => $topic->id,
                        'issue' => $detail['issue'],
                    ]);

                    foreach ($detail['follow_ups'] as $followUp) {
                        try {
                            $deadlineDate = $this->parseDeadline($followUp['deadline']);
                        } catch (\Exception $e) {
                            throw new \Exception('Invalid deadline date format for follow-up: ' . $followUp['deadline']);
                        }

                        SolutionIssue::create([
                            'issue_notulen_id' => $issue->id,
                            'followup' => $followUp['follow_up'],
                            'pic' => $followUp['pic'],
                            'status' => strtolower($followUp['status']),
                            'deadlinedate' => $deadlineDate->format('Y-m-d'),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Notulen dan data terkait berhasil disimpan!',
                'data' => $processedData
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to process data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function progressreportexported($importedData)
    {
        $result = [
            "notulen_number" => "",
            "project_type_id" => null,
            "start_notulen" => "",
            "end_notulen" => "",
            "place" => "",
            "Agenda" => "",
            "list_topic" => []
        ];

        $headerMapping = [
            'notulen_number' => 'B2',
            'start_notulen' => 'D4',
            'end_notulen' => 'F4',
            'place' => 'D6',
            'Agenda' => 'D7',
            'project_type_id' => 'D9'
        ];

        foreach ($headerMapping as $key => $cell) {
            [$row, $col] = $this->cellToIndex($cell);
            if (isset($importedData[$row][$col])) {
                $value = trim($importedData[$row][$col] ?? "");
                if ($key === 'start_notulen' || $key === 'end_notulen') {
                    if (!empty($value)) {
                        $date = $this->parseDeadline($value);
                        $result[$key] = $date->format('d/m/Y');
                    } else {
                        $result[$key] = Carbon::today()->format('d/m/Y');
                    }
                } elseif ($key === 'project_type_id') {
                    $projectType = ProjectType::where('title', $value)->first();
                    $result[$key] = $projectType ? $projectType->id : null;
                } else {
                    $result[$key] = $value;
                }
            }
        }

        // Proses topik dan detail lainnya
        $currentTopicIndex = null;
        $currentIssueIndex = null;

        for ($i = 11; $i < $importedData->count(); $i++) {
            $row = $importedData[$i];

            $topicName = trim($row[1] ?? "");   // Kolom B
            $issue = trim($row[3] ?? "");       // Kolom D
            $followUp = trim($row[5] ?? "");    // Kolom F
            $status = trim($row[7] ?? "");      // Kolom G
            $pic = trim($row[9] ?? "");         // Kolom H
            $deadline = trim($row[11] ?? "");    // Kolom I

            // Deteksi topik baru
            if (!empty($topicName)) {
                $result['list_topic'][] = [
                    'topic' => $topicName,
                    'details' => []
                ];
                $currentTopicIndex = count($result['list_topic']) - 1;
                $currentIssueIndex = null; // Reset issue index karena topik baru
            }

            // Deteksi issue baru
            if ($currentTopicIndex !== null && !empty($issue)) {
                $result['list_topic'][$currentTopicIndex]['details'][] = [
                    'issue' => $issue,
                    'follow_ups' => []
                ];
                $currentIssueIndex = count($result['list_topic'][$currentTopicIndex]['details']) - 1;
            }

            // Deteksi tindak lanjut
            if ($currentTopicIndex !== null && $currentIssueIndex !== null && !empty($followUp)) {
                $normalizedDeadline = $this->parseDeadline($deadline);
                $result['list_topic'][$currentTopicIndex]['details'][$currentIssueIndex]['follow_ups'][] = [
                    'follow_up' => $followUp,
                    'status' => $status ?: 'OPEN',
                    'pic' => $pic ?: 'INKA',
                    'deadline' => $normalizedDeadline->format('d/m/Y')
                ];
            }
        }

        return $result;
    }


    private function parseDeadline($deadline)
    {
        if (empty($deadline) || $deadline === ':') {
            return Carbon::today();
        }

        try {
            // Format YYYY-MM-DD HH:mm:ss
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $deadline)) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $deadline);
            }
            // Format DD/MM/YYYY
            elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $deadline)) {
                return Carbon::createFromFormat('d/m/Y', $deadline);
            }
            // Format YYYY-MM-DD
            elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
                return Carbon::createFromFormat('Y-m-d', $deadline);
            } else {
                Log::warning('Unrecognized deadline format: ' . $deadline);
                return Carbon::today();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to parse deadline: ' . $deadline . '. Error: ' . $e->getMessage());
            return Carbon::today();
        }
    }

    private function cellToIndex($cell)
    {
        preg_match('/([A-Z]+)(\d+)/', $cell, $matches);
        $colLetter = $matches[1];
        $row = (int) $matches[2] - 1;

        $col = 0;
        for ($i = 0; $i < strlen($colLetter); $i++) {
            $col = $col * 26 + (ord(strtoupper($colLetter[$i])) - ord('A') + 1);
        }
        $col--;

        return [$row, $col];
    }

    private function convertMonthToNumber($monthName)
    {
        $months = [
            'januari' => 1,
            'februari' => 2,
            'maret' => 3,
            'april' => 4,
            'mei' => 5,
            'juni' => 6,
            'juli' => 7,
            'agustus' => 8,
            'september' => 9,
            'oktober' => 10,
            'november' => 11,
            'desember' => 12
        ];
        return $months[strtolower($monthName)] ?? 1;
    }



    public function exportNotulenToExcel($notulen_id)
    {
        try {
            $notulen = Notulen::with([
                'files',
                'agendaNotulen',
                'topicnotulens.issueNotulens.solutions'
            ])->findOrFail($notulen_id);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header Styling
            $sheet->mergeCells('B1:H1');
            $sheet->setCellValue('B1', 'NOTULEN RAPAT')
                ->getStyle('B1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Set Information
            $sheet->setCellValue('B2', "Nomor: {$notulen->number}")
                ->setCellValue('B4', 'Hari/Tanggal')
                ->setCellValue('D4', optional(Carbon::parse($notulen->notulen_time_start))->format('d/m/Y'))
                ->setCellValue('E4', 'to')
                ->setCellValue('F4', optional(Carbon::parse($notulen->notulen_time_end))->format('d/m/Y'))
                ->setCellValue('B5', 'Waktu')
                ->setCellValue('D5', '10.00 – selesai')
                ->setCellValue('B6', 'Tempat')
                ->setCellValue('D6', $notulen->place)
                ->setCellValue('B7', 'Agenda')
                ->setCellValue('D7', optional($notulen->agendaNotulen)->name ?? '-')
                ->setCellValue('B8', 'Peserta')
                ->setCellValue('D8', 'Terlampir');

            // Set Table Headers
            $headers = ['Topik', 'Isi Bahasan', 'Tindak Lanjut', 'Status', 'PIC', 'Deadline'];
            $columns = ['B', 'D', 'F', 'H', 'J', 'L'];
            $row = 11;
            foreach ($headers as $index => $header) {
                $sheet->setCellValue("{$columns[$index]}{$row}", $header);
            }

            // Apply Header Styling
            $sheet->getStyle("B{$row}:L{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            // Populate Data
            $row++;
            foreach ($notulen->topicnotulens as $topic) {
                $sheet->setCellValue("B{$row}", $topic->title);
                foreach ($topic->issueNotulens as $issue) {
                    $sheet->setCellValue("D{$row}", $issue->issue);
                    foreach ($issue->solutions as $solution) {
                        $sheet->setCellValue("F{$row}", $solution->followup)
                            ->setCellValue("H{$row}", strtoupper($solution->status))
                            ->setCellValue("J{$row}", $solution->pic)
                            ->setCellValue("L{$row}", optional(Carbon::parse($solution->deadlinedate))->format('d/m/Y'));
                        $row++;
                    }
                    if ($issue->solutions->isEmpty())
                        $row++;
                }
                if ($topic->issueNotulens->isEmpty())
                    $row++;
            }

            // Footer
            $sheet->setCellValue("H2", "Jakarta, " . optional(Carbon::parse($notulen->notulen_time_end))->format('d F Y'))
                ->setCellValue("H3", "Dibuat")
                ->setCellValue("H4", auth()->user()->name)
                ->setCellValue("H6", "Direview")
                ->setCellValue("H7", auth()->user()->name);

            // Apply Borders
            $sheet->getStyle("B11:L{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                ]
            ]);

            // Auto-size columns
            foreach ($columns as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Generate File Name
            $fileName = preg_replace('/[\/:*?"<>|]/', '_', "Notulen_{$notulen->number}.xlsx");
            $filePath = storage_path("app/public/{$fileName}");

            // Save and Download File
            (new Xlsx($spreadsheet))->save($filePath);
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate Excel: ' . $e->getMessage()], 500);
        }
    }




    public function updateSolution(Request $request, $id)
    {
        try {
            $solution = SolutionIssue::findOrFail($id);

            // Perbarui field 'update' jika ada
            if ($request->has('update')) {
                $solution->update = $request->update;
            }

            // Jika status dikirim, validasi dan perbarui
            if ($request->has('status')) {
                $request->validate(['status' => 'required|in:open,closed']);
                $solution->status = $request->status;
            }

            $solution->save();

            // Perbarui status berjenjang
            $this->updateHierarchicalStatus($solution, 'solution');

            return response()->json([
                'success' => true,
                'message' => 'Update solution berhasil disimpan!',
                'data' => [
                    'id' => $solution->id,
                    'update' => $solution->update,
                    'status' => $solution->status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan update: ' . $e->getMessage()
            ], 500);
        }
    }


    // Fungsi untuk menghapus notulen
    public function destroy($id)
    {
        try {
            $notulen = Notulen::findOrFail($id);

            // Hanya izinkan penghapusan jika user adalah pemilik notulen atau user dengan ID 1
            if ($notulen->user_id != Auth::id() && Auth::id() != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus notulen ini.'
                ], 403); // Forbidden
            }

            // Ambil agenda terkait
            $agenda = AgendaNotulen::find($notulen->agenda_notulen_id);

            // Hapus notulen
            $notulen->delete();

            // Cek apakah agenda masih digunakan oleh notulen lain
            $notulenLainnya = Notulen::where('agenda_notulen_id', $agenda->id)->exists();

            // Hapus agenda hanya jika tidak ada notulen lain yang menggunakannya
            if ($agenda && !$notulenLainnya) {
                $agenda->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Notulen berhasil dihapus.' . (!$notulenLainnya ? ' Agenda juga dihapus.' : '')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notulen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $notulen = Notulen::with([
                'files',
                'agendaNotulen',
                'topicnotulens.issueNotulens.solutions'
            ])->findOrFail($id);

            foreach ($notulen->topicnotulens as $topic) {
                foreach ($topic->issueNotulens as $issue) {
                    foreach ($issue->solutions as $solution) {
                        $solution->formatted_deadlinedate = optional(Carbon::parse($solution->deadlinedate))->format('d/m/Y');
                    }
                }
            }

            return response()->json($notulen);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Notulen tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat notulen: ' . $e->getMessage()], 500);
        }
    }
}
