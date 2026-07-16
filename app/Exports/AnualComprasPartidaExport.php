<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromCollection;

class AnualComprasPartidaExport implements FromView
{
    function __construct($data, $partida, $gestion) {
		// function __construct($partida, $afp, $cc, $centralize, $program, $group, $type_generate, $type_render) {
        $this->data = $data;
        $this->partida = $partida;
        $this->gestion = $gestion;

    }

    public function view(): View
    {
        return view('almacenes.report.inventarioAnual.partidaDetallada.excel',
		[   
            'data' => $this->data,
			'partida' => $this->partida,
            'gestion' => $this->gestion
		]);
    }
}