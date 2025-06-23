<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class RentalExport implements FromArray
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $Data;
//    public function collection()
//    {
//        //
//    }
    public function __construct($Data)
    {
        $this->Data = $Data;
    }

    public function array(): array
    {
        return $this->Data;
    }
}
