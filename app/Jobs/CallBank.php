<?php

namespace App\Jobs;

use App\Http\Controllers\BankRequestController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CallBank implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $bankAcc;

    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bankAcc)
    {
        $this->bankAcc = $bankAcc;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $args = [
            'from_date' => 20220901,
            'to_date' => 20221003,
            'acno' => $this->bankAcc
        ];

       (new \App\Http\Controllers\BankRequestController)->callBank($args);
    }



}
