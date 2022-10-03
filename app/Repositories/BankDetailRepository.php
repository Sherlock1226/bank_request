<?php

namespace App\Repositories;

use App\Interfaces\EloquentRepositoryInterface;
use App\Models\BankDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BankDetailRepository extends BaseRepository implements EloquentRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(BankDetail $model)
    {
        parent::__construct($model);
    }


    /**
     * @param array $data
     * @return void
     */
    public function saveData(array $data): void
    {
//        protected $fillable = [
//        'BANKID',//銀行代號
//        'BACCNO',//銀行帳號
//        'TXDATE',//交易日期
//        'TXTIME',//交易時間 (hhmmssss)
//        'TXSEQNO',//交易序號
//        'TXIDNO',//交易代號
//        'CHKNO',//支票號碼
//        'AMOUNT',//交易金額
//        'SIGN',//交易金額正負號
//        'BAMOUNT',//帳戶餘額
//        'BSIGN',//帳戶餘額正負號
//        'MEMO1',//備註一
//        'MEMO2',//備註二
//        'XBANKID',//對方行
//        'CURY',//幣別
//        'DC',//借貸別1:借;2:貸(存)
//        'TX_SPEC',//交易說明
//        'ACCNAME',//戶名
//    ];
        $this->BANKID = $data['BANKID'];
        $this->model->save();

    }

}
