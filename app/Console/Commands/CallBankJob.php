<?php

namespace App\Console\Commands;

use App\Jobs\CallBank;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CallBankJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:callbankjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '資料庫撈取帳號至銀行撈取交易資料';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $data = DB::table('bankacc')
            ->select('BACCNO')
            ->where('BANKID', '013')->get();
        $array = json_decode(json_encode($data), true);

        print_r($array);
        foreach ($array as $k){
            CallBank::dispatch($k['BACCNO'])->delay(60 * 4);
        }
    }

}
