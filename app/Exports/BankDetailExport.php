<?php

namespace App\Exports;

use App\Models\BankDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class BankDetailExport implements
    FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithEvents,
    ShouldAutoSize, WithColumnWidths

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

        $from_date = date('Y-m-d', strtotime($this->data['from_date'])) ?? date('Ymd', strtotime("-1 days"));
        $to_date = date('Y-m-d', strtotime($this->data['to_date'])) ?? date('Ymd', strtotime("-1 days"));

        //todo 業務
        return BankDetail::query()
            ->where('BACCNO', $this->data['bank_acc'])
            ->whereDate('TXDATE', '>=', $from_date)
            ->whereDate('TXDATE', '<=', $to_date)
            ->get();


//        return json_decode($this->data,true);
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
            ['戶名'],
            ['銀行名稱'],
            [' 日   期 ', ' 摘  要 ', '支   出(借)', '存   入(貸)', '結   存'],
            ['上年度結轉餘額'],

//            ['日期', '銀行帳號', '交易序號', '交易代號', '支票號碼', '交易金額正負號', '交易金額', '帳戶餘額正負號', '帳戶餘額', '備註一', '備註二', '幣別', '交易說明']

        ];
    }

    /**
     * @throws Exception
     */
    public function styles(Worksheet $sheet)
    {

        //合併第一列
        $sheet->mergeCells("A1:F1");
        $sheet->mergeCells("A2:F2");

        //在第一格中寫入的相關資料
        $sheet->setCellValue("A1", '偉柏工業');
        //在第二格中寫入的相關資料
        $sheet->setCellValue("A2", "銀行名稱：國泰世華");

    }

    /**
     * @var BankDetail $bankDetail
     */
    public function map($bankDetail): array
    {
        // TODO: Implement map() method.
        $dt1 = $dt2 = 0;
        $amount = number_format($bankDetail->AMOUNT,2);
        $bamount = number_format($bankDetail->BAMOUNT,2);
        if ($bankDetail->DC == 2) {
            $dt2 = $amount;
        } else {
            $dt1 = $amount;
        }
        return [
            $bankDetail->TXDATE,
            $bankDetail->MEMO1 . $bankDetail->TX_SPEC . $bankDetail->BACCNO . $bankDetail->MEMO2,
            $dt1,
            $dt2,
            $bamount,
        ];
    }


//    /**
//     * @var BankDetail $bankDetail
//     */
//    public function map($bankDetail): array
//    {
//        // TODO: Implement map() method.
//        return [
//            $bankDetail->TXDATE,
//            $bankDetail->BACCNO,
//            $bankDetail->TXSEQNO,
//            $bankDetail->TXIDNO,
//            $bankDetail->CHKNO,
//            $bankDetail->SIGN,
//            $bankDetail->AMOUNT,
//            $bankDetail->BSIGN,
//            $bankDetail->BAMOUNT,
//            $bankDetail->MEMO1,
//            $bankDetail->MEMO2,
//            $bankDetail->CURY,
//            $bankDetail->TX_SPEC,
//        ];
//    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'C' => NumberFormat::FORMAT_NUMBER_00, //金額保留兩位小數
            'D' => NumberFormat::FORMAT_NUMBER_00, //金額保留兩位小數
            'E' => NumberFormat::FORMAT_NUMBER_00, //金額保留兩位小數
        ];
    }

    /*設定每一行的寬度*/
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 60,
            'C' => 20,
            'D' => 20,
            'E' => 20,
        ];
    }


    public function registerEvents(): array
    {

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->mergeCells('A1:E1');
                $event->sheet->getDelegate()->mergeCells('A2:B2');
                $event->sheet->getDelegate()->mergeCells('C2:E2');
                $event->sheet->getDelegate()->mergeCells('A4:E4');

                //设置区域单元格垂直居中
                $event->sheet->getDelegate()->getStyle('A1:K1265')->getAlignment()->setVertical('center');

                $event->sheet->setCellValue("A1", '偉柏工業');
                $event->sheet->setCellValue("A2", "銀行名稱：國泰世華");
                $event->sheet->setCellValue("C2", $this->data['bank_acc']);

                $event->sheet->getDelegate()->getStyle('A4:E4')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('CECECE');


            },
        ];
    }


//    public function view(): View
//    {
//        // TODO: Implement view() method.
//        return view('exports.bankDetail', [
//            'bankDetail' => $this->createData()
//        ]);
//    }
}
