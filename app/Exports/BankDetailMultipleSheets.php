<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class BankDetailMultipleSheets implements WithMultipleSheets
{
    private $data;
    private $cur;

    public function __construct($data,$cur)
    {
        $this->data = $data;
        $this->cur  = $cur;
    }



    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->cur as $k => $v){
            $sheets[] = new BankDetailExport($this->data,$v);
        }

        return $sheets;
    }
}
