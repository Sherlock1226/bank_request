<?php

namespace App\Exports;

use App\Models\BankAcc;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class BankDetailMultipleSheets implements WithMultipleSheets
{
    private $data;
    private $cur;

    public function __construct($data, $cur)
    {
        $this->data = $data;
        $this->cur = $cur;
    }


    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->cur as $k => $v) {
            foreach ($v as $value) {
                $this->data['bank_acc'] = $k;
                $bankAcc = $this->getBankAccByAccNum($k);
                $sheets[] = new BankDetailExport($this->data, $value, $bankAcc);
            }

        }

        return $sheets;
    }


    /**
     * @param $bankACC
     * @return array|Collection
     */
    public function getBankAccByAccNum($bankACC)
    {
        $rs = [];
        try {
            $data = DB::table('bankacc')
                ->select(DB::raw('*'))
                ->where('BACCNO', $bankACC)->get();

            $rs = json_decode(json_encode($data[0]), true);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $rs;
    }
}
