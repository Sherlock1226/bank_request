<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_detail', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('BANKID')->nullable()->comment('銀行代號');
            $table->integer('BACCNO')->comment('銀行帳號');
            $table->dateTime('TXDATE')->comment('交易日期');
            $table->string('TXTIME')->comment('交易時間 (hhmmssss)');
            $table->string('TXSEQNO')->unique()->comment('交易序號');
            $table->string('TXIDNO')->comment('交易代號');
            $table->string('CHKNO')->comment('支票號碼');
            $table->integer('AMOUNT')->comment('交易金額');
            $table->string('SIGN')->comment('交易金額正負號');
            $table->integer('BAMOUNT')->comment('帳戶餘額');
            $table->string('BSIGN')->comment('帳戶餘額正負號');
            $table->string('MEMO1')->nullable()->comment('備註一');
            $table->string('MEMO2')->nullable()->comment('備註二');
            $table->string('XBANKID')->nullable()->comment('對方行');
            $table->string('CURY')->comment('幣別');
            $table->string('DC')->comment('借貸別1:借;2:貸(存)');
            $table->string('TX_SPEC')->nullable()->comment('交易說明');
            $table->string('ACCNAME')->nullable()->comment('戶名');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_detail');
    }
}
