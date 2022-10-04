<?php

namespace App\Exports;

use App\Models\BankDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class BankDetailExport implements
    FromCollection, WithHeadings, WithMapping, WithColumnFormatting,WithEvents
{
    /**
     * @return Collection
     */

    protected $data;

    //建構函式傳值
    protected $collection;

    public function __construct($data)
    {
        $this->data = $data;
    }

    //陣列轉集合
    public function collection()
    {
        return new Collection($this->createData());
    }

    //業務程式碼
    public function createData()
    {
        //todo 業務
        return BankDetail::all();
    }

    /**
     * 設定標題列
     * @return \string[][]
     */
    public function headings(): array
    {
        // TODO: Implement headings() method.
        //第一列為先放一個空白的資料，後面會取代掉
        return [
            ['row1'],
            ['銀行名稱'],
            ['日期', '銀行帳號', '交易序號', '交易代號', '支票號碼', '交易金額正負號', '交易金額', '帳戶餘額正負號', '帳戶餘額', '備註一', '備註二', '幣別', '交易說明']
        ];
    }

    /**
     * @throws Exception
     */
    public function styles(Worksheet $sheet)
    {

        //合併第一列
        $sheet->mergeCells("A1:M1");
        $sheet->mergeCells("A2:M2");

        //在第一格中寫入的相關資料
        $sheet->setCellValue("A1", '');
        //在第二格中寫入的相關資料
        $sheet->setCellValue("A2", "銀行名稱：國泰世華");

    }

    /**
     * @var BankDetail $bankDetail
     */
    public function map($bankDetail): array
    {
        // TODO: Implement map() method.
        return [
            $bankDetail->TXDATE,
            $bankDetail->BACCNO,
            $bankDetail->TXSEQNO,
            $bankDetail->TXIDNO,
            $bankDetail->CHKNO,
            $bankDetail->SIGN,
            $bankDetail->AMOUNT,
            $bankDetail->BSIGN,
            $bankDetail->BAMOUNT,
            $bankDetail->MEMO1,
            $bankDetail->MEMO2,
            $bankDetail->CURY,
            $bankDetail->TX_SPEC,
        ];
    }


    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->mergeCells('A1:M1');
                $event->sheet->getDelegate()->getStyle('A2:M2')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('CECECE');
            },
        ];
    }


}
