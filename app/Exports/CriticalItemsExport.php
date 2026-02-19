<?php

namespace App\Exports;

use App\Models\FmecaItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CriticalItemsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return FmecaItem::where(function ($query) {
            $query->whereIn('safety_risk_level', ['Undesirable', 'Intolerable'])
                ->orWhereIn('reliability_risk_level', ['Undesirable', 'Intolerable']);
        })->with(['fmecaPart.fmecaIdentity.projectType'])->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Safety Risk Level', 'Reliability Risk Level', 'Project Type'];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->item_name, // pastikan field yang benar
            $item->safety_risk_level,
            $item->reliability_risk_level,
            $item->fmecaPart->fmecaIdentity->projectType->name ?? 'N/A',
        ];
    }
}
