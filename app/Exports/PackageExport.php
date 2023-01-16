<?php

namespace App\Exports;

use App\Models\Package;
use Maatwebsite\Excel\Concerns\FromCollection;

class PackageExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Package::all();
    }

    /**
     * @return array
     */
    // public function headings(): array
    // {
    //     return [
    //         '#',
    //         '',
    //         'Email',
    //         'Created At',
    //         'Updated At',
    //     ];
    // }
}
