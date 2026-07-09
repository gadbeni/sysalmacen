<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class PeopleExtExport implements FromView
{
    protected $datas;

    function __construct($data)
    {
        $this->datas = $data;
    }

    public function view(): View
    {
        return view('almacenes.peopleExt.excel',
        [
            'data' => $this->datas,
        ]);
    }
}
