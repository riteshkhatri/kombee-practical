<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Supplier::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Contact Number',
            'Address',
            'Created At',
        ];
    }

    /**
     * @param mixed $supplier
     * @return array
     */
    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->name,
            $supplier->email,
            $supplier->contact_number,
            $supplier->address,
            $supplier->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
