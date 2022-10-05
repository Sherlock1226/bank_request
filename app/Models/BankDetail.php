<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class BankDetail extends Model
{

    use Notifiable;

    /**
     * @var mixed
     */
    protected $table = 'bank_detail';

    protected $fillable = [
        'BANKID',//銀行代號
        'BACCNO',//銀行帳號
        'TXDATE',//交易日期
        'TXTIME',//交易時間 (hhmmssss)
        'TXSEQNO',//交易序號
        'TXIDNO',//交易代號
        'CHKNO',//支票號碼
        'AMOUNT',//交易金額
        'SIGN',//交易金額正負號
        'BAMOUNT',//帳戶餘額
        'BSIGN',//帳戶餘額正負號
        'MEMO1',//備註一
        'MEMO2',//備註二
        'XBANKID',//對方行
        'CURY',//幣別
        'DC',//借貸別1:借;2:貸(存)
        'TX_SPEC',//交易說明
        'ACCNAME',//戶名
        'created_at',
        'updated_at',
    ];

}
