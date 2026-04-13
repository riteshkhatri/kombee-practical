<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return User::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Contact Number',
            'Gender',
            'Postcode',
            'Created At',
        ];
    }

    /**
     * @param  mixed  $user
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->contact_number,
            $user->gender,
            $user->postcode,
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
