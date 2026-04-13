<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return Customer::all();
    }

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
     * @param  mixed  $customer
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->email,
            $customer->contact_number,
            $customer->address,
            $customer->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
