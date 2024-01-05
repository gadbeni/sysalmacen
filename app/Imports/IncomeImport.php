<?php

namespace App\Imports;

use App\Models\Imports\ImportSolicitudCompra;
use Maatwebsite\Excel\Concerns\ToModel;

class IncomeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    protected $user;
    protected $unidadadministrativa;

    public function __construct($user,$unidadadministrativa)
    {
        $this->user = $user;
        $this->unidadadministrativa = $unidadadministrativa;
    }

    public function model(array $row)
    {
        $unidad = DB::connection('mamore')->table()

        return new ImportSolicitudCompra([
            //
        ]);
    }
}
