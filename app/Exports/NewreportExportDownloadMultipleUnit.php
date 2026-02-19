<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class NewreportExportDownloadMultipleUnit implements WithMultipleSheets
{
    protected $groupedProgressReports;

    public function __construct($groupedProgressReports)
    {
        $this->groupedProgressReports = $groupedProgressReports;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->groupedProgressReports as $unit => $progressReports) {
            $sheets[] = new class($progressReports, $unit) implements FromView, WithColumnWidths, WithStyles {
                protected $progressReports;
                protected $unit;

                public function __construct($progressReports, $unit)
                {
                    $this->progressReports = $progressReports;
                    $this->unit = $unit;
                }

                public function view(): View
                {
                    $processedReports = array_map(function ($report) {
                        $report->startreleasedate = $report->startreleasedate
                            ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($report->startreleasedate)
                            : '';
                        $report->deadlinereleasedate = $report->deadlinereleasedate
                            ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($report->deadlinereleasedate)
                            : '';
                        $report->realisasidate = $report->realisasidate
                            ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($report->realisasidate)
                            : '';
                        return $report;
                    }, $this->progressReports);

                    return view('newreports.exports.newreportdownloadmultipleunit', [
                        'progressreport' => $processedReports
                    ]);
                }

                public function title(): string
                {
                    return substr(preg_replace('/[^A-Za-z0-9\-]/', '_', $this->unit), 0, 31);
                }

                public function columnWidths(): array
                {
                    return [
                        'A' => 10,
                        'B' => 20,
                        'C' => 30,
                        'D' => 30,
                        'E' => 25,
                        'F' => 15,
                        'G' => 15,
                        'H' => 15,
                        'I' => 15,
                        'J' => 15,
                        'K' => 15,
                        'L' => 15,
                        'M' => 20,
                        'N' => 15,
                        'O' => 15,
                        'P' => 10,
                        'Q' => 15,
                        'R' => 15,
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    \PhpOffice\PhpSpreadsheet\Settings::setLocale('id_ID');
                    $sheet->getStyle('J')->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                    $sheet->getStyle('K')->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                    $sheet->getStyle('L')->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                    $sheet->getStyle('A1:R1')->getFont()->setBold(true);
                    return [];
                }
            };
        }

        // Resume sheet (unchanged)
        $sheets[] = new class($this->groupedProgressReports) implements FromView, WithColumnWidths {
            protected $groupedProgressReports;

            public function __construct($groupedProgressReports)
            {
                $this->groupedProgressReports = $groupedProgressReports;
            }

            public function view(): View
            {
                $resumeData = [];
                foreach ($this->groupedProgressReports as $unit => $progressReports) {
                    $resumeData[] = [
                        'unit' => $unit,
                        'document_count' => count($progressReports)
                    ];
                }

                return view('newreports.exports.resume', [
                    'resumeData' => $resumeData
                ]);
            }

            public function title(): string
            {
                return 'Resume';
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 30,
                    'B' => 20,
                ];
            }
        };

        return $sheets;
    }
}
