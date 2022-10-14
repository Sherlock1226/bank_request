<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


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

        Log::info('CallBank handle');
        $args = [
            'acno' => $this->bankAcc
        ];
        $this->release(300);

        app(\App\Http\Controllers\BankRequestController::class)->callBank($args);
        Log::info('CallBank release');
        Log::info('CallBank end');
    }


}
