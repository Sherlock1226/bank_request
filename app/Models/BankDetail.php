<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class BankDetail extends Model
{

    use Notifiable;
    //        $table->integer('BANKID')->nullable()->comment('銀行代號');
    //            $table->integer('BACCNO')->comment('銀行帳號');
    //            $table->dateTime('TXDATE')->comment('交易日期');
    //            $table->string('TXTIME')->comment('交易時間 (hhmmssss)');
    //            $table->string('TXSEQNO')->unique()->comment('交易序號');
    //            $table->string('TXIDNO')->comment('交易代號');
    //            $table->string('CHKNO')->comment('支票號碼');
    //            $table->integer('AMOUNT')->comment('交易金額');
    //            $table->string('SIGN')->comment('交易金額正負號');
    //            $table->integer('BAMOUNT')->comment('帳戶餘額');
    //            $table->string('BSIGN')->comment('帳戶餘額正負號');
    //            $table->string('MEMO1')->nullable()->comment('備註一');
    //            $table->string('MEMO2')->nullable()->comment('備註二');
    //            $table->string('XBANKID')->nullable()->comment('對方行');
    //            $table->string('CURY')->comment('幣別');
    //            $table->string('DC')->comment('借貸別1:借;2:貸(存)');
    //            $table->string('TX_SPEC')->nullable()->comment('交易說明');
    //            $table->string('ACCNAME')->nullable()->comment('戶名');

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
    ];

}
