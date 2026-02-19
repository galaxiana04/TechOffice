<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class NewreportExportDownload implements FromView, WithColumnWidths, WithStyles
{
    protected $progressReports;

    public function __construct($hasil)
    {
        $this->progressReports = $hasil;
    }

    public function view(): View
    {
        $processedReports = array_map(function ($report) {
            try {
                $report->startreleasedate = $report->startreleasedate
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\Carbon\Carbon::parse($report->startreleasedate))
                    : '';
                $report->deadlinereleasedate = $report->deadlinereleasedate
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\Carbon\Carbon::parse($report->deadlinereleasedate))
                    : '';
                $report->realisasidate = $report->realisasidate
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\Carbon\Carbon::parse($report->realisasidate))
                    : '';
            } catch (\Exception $e) {
                $report->startreleasedate = '';
                $report->deadlinereleasedate = '';
                $report->realisasidate = '';
            }

            return $report;
        }, $this->progressReports);

        return view('newreports.exports.newreportdownload', [
            'progressreport' => $processedReports
        ]);
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
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('K1:K' . $highestRow)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
        $sheet->getStyle('L1:L' . $highestRow)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
        $sheet->getStyle('M1:M' . $highestRow)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
        $sheet->getStyle('A1:R1')->getFont()->setBold(true);
        return [];
    }
}
