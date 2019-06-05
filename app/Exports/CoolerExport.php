<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class CoolerExport implements FromView
{
    private $type=1;
    private $coolers;
    public function __construct($type,$coolers)
    {
        $this->type=$type;
        $this->coolers=$coolers;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        switch ($this->type){
            case 1:
                return view('exports.count_cooler_number', [
                    'coolers' => $this->coolers
                ]);
                break;
            case 2:
                return view('exports.count_cooler_volume', [
                    'coolers' => $this->coolers
                ]);
                break;
            case 3:
                return view('exports.count_cooler_status', [
                    'coolers' => $this->coolers
                ]);
                break;
        }

    }
}
