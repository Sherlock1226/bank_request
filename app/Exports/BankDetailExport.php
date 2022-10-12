<?php

namespace App\Exports;

use App\Models\BankAcc;
use App\Models\BankDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class BankDetailExport implements
     WithHeadings, WithMapping, WithColumnFormatting, WithEvents,
    ShouldAutoSize, WithColumnWidths, WithTitle,FromQuery,FromCollection

{

    use Exportable;

    protected $data;

    //建構函式傳值
    protected $collection;
    protected $cur;
    private $bankAcc;

    public function __construct($data,$cur,$bankAcc)
    {
        $this->data = $data;
        $this->cur  = $cur;
        $this->bankAcc = $bankAcc;
    }

    //陣列轉集合
    public function collection(): Collection
    {
        return new Collection($this->query());
    }


    /**
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function query()
    {
        $from_date = date('Y-m-d', strtotime($this->data['from_date'])) ?? date('Ymd', strtotime("-1 days"));
        $to_date = date('Y-m-d', strtotime($this->data['to_date'])) ?? date('Ymd', strtotime("-1 days"));


        return BankDetail::query()
            ->where('BACCNO', $this->data['bank_acc'])
            ->where('CURY', $this->cur)
            ->whereDate('TXDATE', '>=', $from_date)
            ->whereDate('TXDATE', '<=', $to_date)
            ->get();

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

        ];
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

    /**
     * @return string
     */
    public function title(): string
    {
        return mb_substr($this->bankAcc['BANKNAME'],0,2).'#'.substr($this->data['bank_acc'],-7).'('.$this->bankAcc['ACCNAME_A'].')'.'-'.$this->cur;
    }

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

                $event->sheet->setCellValue("A1", $this->bankAcc['ACCNAME']);
                $event->sheet->setCellValue("A2", "銀行名稱： ".$this->bankAcc['BANKNAME']);
                $event->sheet->setCellValue("C2", $this->data['bank_acc']);

                $event->sheet->getDelegate()->getStyle('A4:E4')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('CECECE');


            },
        ];
    }



}
