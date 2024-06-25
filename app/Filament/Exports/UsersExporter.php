<?php

namespace App\Filament\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExporter implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nome Completo',
            'Email',
            'Função',
            'Data Validado',
            'Data Criado',
            'Data Atualizado',
        ];
    }

    /**
     * @param mixed $user
     *
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->roles->pluck('name')->implode(', '),
            $user->email_verified_at ? $user->email_verified_at->format('d/m/Y H:i:s') : '',
            $user->created_at->format('d/m/Y H:i:s'),
            $user->updated_at->format('d/m/Y H:i:s'),
        ];
    }
}
