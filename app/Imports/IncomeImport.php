<?php

namespace App\Imports;

use App\Models\SolicitudCompra;
use Maatwebsite\Excel\Concerns\ToModel;

class IncomeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new SolicitudCompra([
            //
        ]);
    }
}
