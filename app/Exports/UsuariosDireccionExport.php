<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class UsuariosDireccionExport implements FromView
{
    function __construct($data, $sucursal)
    {
        $this->datas    = $data;
        $this->sucursal = $sucursal;
    }

    public function view(): View
    {
        return view('almacenes.report.aditional.usuariosDireccion.excel',
        [
            'data'     => $this->datas,
            'sucursal' => $this->sucursal,
        ]);
    }
}
