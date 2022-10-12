<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankaccTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bankacc', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('BANKID')->nullable()->comment('銀行代號');
            $table->string('BANKNAME')->nullable()->comment('銀行名稱');
            $table->string('BACCNO')->comment('銀行帳號');
            $table->string('ACCNAME')->nullable()->nullable()->comment('戶名');
            $table->string('ACCNAME_A')->nullable()->nullable()->comment('戶名縮寫');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bankacc');
    }
}
